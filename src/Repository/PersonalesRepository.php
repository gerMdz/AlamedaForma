<?php

namespace App\Repository;

use App\Entity\Personales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personales>
 *
 * @method Personales|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personales|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personales[]    findAll()
 * @method Personales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personales::class);
    }

//    /**
//     * @return Personales[] Returns an array of Personales objects
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

//    public function findOneBySomeField($value): ?Personales
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
