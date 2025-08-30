<?php

namespace App\Controller\Api;

use App\DTO\PersonalesFormationDataTransformer;
use App\Repository\PersonalFormationRepository;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonalFormationController extends AbstractController
{
    public function __construct(
        private readonly PersonalFormationRepository $personalFormationRepository,
        private readonly ManagerRegistry $registry
    ) {
    }

    /**
     * @throws JsonException
     */
    public function __invoke(Request $request): Response
    {
        $data = $request->getContent();

        if (empty($data)) {
            return $this->json(['error' => 'No Se recibieron datos'], 400);
        }

        $dto = new PersonalesFormationDataTransformer(
            json_decode($data, true, 512, JSON_THROW_ON_ERROR),
            $this->registry
        );

        $entity = $dto->transform();

        // Validaciones: evitar NOT NULL en BD
        if ($entity->getPerson() === null) {
            return $this->json(['error' => 'Falta la persona (person) o no se pudo resolver.'], 400);
        }
        if ($entity->getDon() === null) {
            return $this->json(['error' => 'Falta el don (don) o no se pudo resolver.'], 400);
        }
        if ($entity->getPercentDon() === null) {
            return $this->json(['error' => 'Falta percentDon.'], 400);
        }

        $saved = $this->personalFormationRepository->guardar($entity);

        return $this->json($saved, 201);
    }
}