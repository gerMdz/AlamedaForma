<?php

namespace App\State;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalOrientacionDetalle;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PersonalOrientacionDetalleProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param PersonalOrientacionDetalle $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PersonalOrientacionDetalle) {
            return $data;
        }

        $now = new DateTimeImmutable();
        $isCreate = $data->getId() === null;
        if ($isCreate) {
            $data->setCreatedAt($now);
        }
        $data->setUpdatedAt($now);

        // Enforce a maximum of 3 detalles per PersonalOrientacion with a user-friendly 422
        if ($data->getPersonalOrientacion() !== null) {
            $po = $data->getPersonalOrientacion();
            // Count existing rows for this PO
            $qb = $this->em->createQueryBuilder()
                ->select('COUNT(pod.id)')
                ->from(PersonalOrientacionDetalle::class, 'pod')
                ->where('pod.personalOrientacion = :po')
                ->setParameter('po', $po);
            try {
                $count = (int)$qb->getQuery()->getSingleScalarResult();
            } catch (NoResultException|NonUniqueResultException $e) {
                $count = 0;
            }
            if ($isCreate && $count >= 3) {
                throw new InvalidArgumentException('Solo se permiten hasta 3 DetalleOrientacion por PersonalOrientacion.');
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
