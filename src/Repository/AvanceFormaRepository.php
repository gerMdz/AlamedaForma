<?php

namespace App\Repository;

use App\Entity\AvanceForma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvanceForma>
 *
 * @method AvanceForma|null find($id, $lockMode = null, $lockVersion = null)
 * @method AvanceForma|null findOneBy(array $criteria, array $orderBy = null)
 * @method AvanceForma[]    findAll()
 * @method AvanceForma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvanceFormaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvanceForma::class);
    }
}
