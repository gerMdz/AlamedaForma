<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalOrientacion;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PersonalOrientacionProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param PersonalOrientacion $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PersonalOrientacion) {
            return $data;
        }

        $now = new DateTimeImmutable();
        if ($data->getId() === null) {
            // Creating
            $data->setCreatedAt($now);
        }
        $data->setUpdatedAt($now);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
