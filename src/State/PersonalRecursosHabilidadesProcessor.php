<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalRecursosHabilidades;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PersonalRecursosHabilidadesProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param PersonalRecursosHabilidades $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PersonalRecursosHabilidades) {
            return $data;
        }

        $now = new DateTimeImmutable();

        if ($data->getId() === null) {
            $data->setCreatedAt($now);
        }
        $data->setUpdatedAt($now);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
