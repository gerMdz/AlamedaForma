<?php

namespace App\Repository;

use App\Entity\PersonalRecursosHabilidades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalRecursosHabilidades>
 */
class PersonalRecursosHabilidadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalRecursosHabilidades::class);
    }
}
