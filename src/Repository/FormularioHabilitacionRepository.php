<?php

namespace App\Repository;

use App\Entity\FormularioHabilitacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormularioHabilitacion>
 *
 * @method FormularioHabilitacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormularioHabilitacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormularioHabilitacion[]    findAll()
 * @method FormularioHabilitacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormularioHabilitacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormularioHabilitacion::class);
    }

    /**
     * Encuentra habilitaciones activas para un nombre de formulario dado en una fecha/hora concreta.
     */
    public function findActivasPorNombre(string $nombreFormulario, \DateTimeImmutable $at = new \DateTimeImmutable()): array
    {
        $qb = $this->createQueryBuilder('h');
        return $qb
            ->andWhere('h.nombreFormulario = :nombre')
            ->andWhere('h.activoDesde <= :at')
            ->andWhere('(h.activoHasta IS NULL OR h.activoHasta >= :at)')
            ->setParameter('nombre', $nombreFormulario)
            ->setParameter('at', $at)
            ->orderBy('h.activoDesde', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
