<?php

namespace App\Repository;

use App\Entity\Forma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Forma>
 *
 * @method Forma|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forma|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forma[]    findAll()
 * @method Forma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forma::class);
    }

//    /**
//     * @return Forma[] Returns an array of Forma objects
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

//    public function findOneBySomeField($value): ?Forma
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
