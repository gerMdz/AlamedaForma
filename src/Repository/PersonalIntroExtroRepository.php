<?php

namespace App\Repository;

use App\Entity\PersonalIntroExtro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalIntroExtro>
 */
class PersonalIntroExtroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalIntroExtro::class);
    }
}
