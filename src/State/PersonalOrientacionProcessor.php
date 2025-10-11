<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AvanceForma;
use App\Entity\FormularioHabilitacion;
use App\Entity\PersonalOrientacion;
use App\Entity\Personales;
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

        // Helper: verificar si la persona ya tiene Avance para 'O'
        $hasAvanceO = function (?Personales $persona): bool {
            if (!$persona) return false;
            $formO = $this->em->getRepository(FormularioHabilitacion::class)
                ->findOneBy(['identifier' => 'O'], ['activoDesde' => 'DESC']);
            if (!$formO) return false;
            $existingAvance = $this->em->getRepository(AvanceForma::class)
                ->findOneBy(['persona' => $persona, 'formulario' => $formO]);
            return (bool)$existingAvance;
        };

        // Si es PATCH/PUT sobre entidad existente, y ya hay Avance O, evitar cambios
        if ($data->getId() !== null) {
            $persona = $data->getPersona();
            if ($hasAvanceO($persona)) {
                // Descartar cambios no persistidos y devolver el estado actual
                try { $this->em->refresh($data); } catch (\Throwable) {}
                return $data;
            }
        }

        // Upsert por persona: si viene un POST sin id pero con persona ya existente, actualizamos en lugar de crear
        if ($data->getId() === null && $data->getPersona() !== null) {
            $repo = $this->em->getRepository(PersonalOrientacion::class);
            $existing = $repo->findOneBy(['persona' => $data->getPersona()]);
            if ($existing instanceof PersonalOrientacion) {
                // Si ya tiene Avance O, no permitir actualizar (devolver existente sin cambios)
                if ($hasAvanceO($existing->getPersona())) {
                    return $existing;
                }
                // Copiar campos editables
                $existing
                    ->setAction1($data->getAction1())
                    ->setAction2($data->getAction2())
                    ->setAction3($data->getAction3())
                    ->setTrabajar($data->getTrabajar())
                    ->setResolver($data->getResolver())
                ;
                // mantener createdAt original, solo actualizar updatedAt
                $existing->setUpdatedAt($now);

                $this->em->flush();
                return $existing;
            }
            // Si no existe aún pero ya hay avance O, no crear
            if ($hasAvanceO($data->getPersona())) {
                // Devolver tal cual sin persistir
                return $data;
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
