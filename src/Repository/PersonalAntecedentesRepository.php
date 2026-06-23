<?php

namespace App\Repository;

use App\Entity\PersonalAntecedentes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalAntecedentes>
 */
class PersonalAntecedentesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalAntecedentes::class);
    }
}
