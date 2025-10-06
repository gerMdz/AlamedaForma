<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PersonalRecursos;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class PersonalRecursosProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param PersonalRecursos $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PersonalRecursos) {
            return $data;
        }

        $now = new DateTimeImmutable();

        // Upsert por persona: si viene un POST sin id pero con persona ya existente, actualizamos en lugar de crear
        if ($data->getId() === null && $data->getPersona() !== null) {
            $repo = $this->em->getRepository(PersonalRecursos::class);
            $existing = $repo->findOneBy(['persona' => $data->getPersona()]);
            if ($existing instanceof PersonalRecursos) {
                // Copiar campos editables
                $existing
                    ->setVocacion($data->getVocacion())
                    ->setTrabajos($data->getTrabajos())
                    ->setClases($data->getClases())
                    ->setContribucion($data->getContribucion())
                ;
                // mantener createdAt original, solo actualizar updatedAt
                $existing->setUpdatedAt($now);

                $this->em->flush();
                return $existing;
            }
        }

        if ($data->getId() === null) {
            // Crear nuevo registro
            $data->setCreatedAt($now);
        }
        $data->setUpdatedAt($now);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
