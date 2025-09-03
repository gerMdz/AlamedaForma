<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\Entity\PersonalOrientacionDetalle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

#[AllowDynamicProperties]
class PersonalOrientacionDetalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalOrientacionDetalle::class);
    }
}
