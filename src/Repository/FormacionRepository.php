<?php

namespace App\Repository;

use App\Entity\Formacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formacion>
 *
 * @method Formacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formacion[]    findAll()
 * @method Formacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formacion::class);
    }

//    /**
//     * @return Formacion[] Returns an array of Formacion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formacion
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
