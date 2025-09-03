<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\Entity\PersonalOrientacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

#[AllowDynamicProperties]
class PersonalOrientacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalOrientacion::class);
    }
}
