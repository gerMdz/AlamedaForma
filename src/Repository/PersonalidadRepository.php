<?php

namespace App\Repository;

use App\Entity\Personalidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personalidad>
 *
 * @method Personalidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personalidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personalidad[]    findAll()
 * @method Personalidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personalidad::class);
    }

    //    /**
    //     * @return Personalidad[] Returns an array of Personalidad objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Personalidad
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
