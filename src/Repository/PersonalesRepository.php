<?php

namespace App\Repository;

use App\Entity\Personales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Personales::class);
        $this->entityManager = $entityManager;
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
    public function save( $personales): void
    {
        $this->entityManager->persist($personales);
        $this->entityManager->flush();
    }


    public function guardar($personales)
    {
        $this->entityManager->persist($personales);
        $this->entityManager->flush();

        return $personales;

    }
}
