<?php

namespace App\Repository;

use App\Entity\PersonalDisc;
use App\Entity\Personales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalDisc>
 */
class PersonalDiscRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalDisc::class);
    }

    /**
     * @return PersonalDisc[]
     */
    public function findByPerson(Personales $person, int $limit = 50, int $offset = 0): array
    {
        return $this->createQueryBuilder('pd')
            ->andWhere('pd.person = :p')
            ->setParameter('p', $person)
            ->orderBy('pd.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findLatestByPerson(Personales $person): ?PersonalDisc
    {
        return $this->createQueryBuilder('pd')
            ->andWhere('pd.person = :p')
            ->setParameter('p', $person)
            ->orderBy('pd.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
