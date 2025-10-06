<?php

namespace App\Controller\Api;

use App\Entity\PersonalRecursos;
use App\Entity\Personales;
use App\Repository\PersonalRecursosRepository;
use App\Repository\PersonalesRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PersonalRecursosController extends AbstractController
{
    public function __construct(
        private readonly PersonalesRepository $personalesRepo,
        private readonly PersonalRecursosRepository $recursosRepo,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/api/personal-recursos', name: 'api_personal_recursos_upsert', methods: ['POST','PUT','PATCH'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function upsert(Request $request): JsonResponse
    {
        $payload = json_decode((string)$request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'JSON inválido'], 400);
        }

        $personaId = $payload['persona'] ?? null;
        if (!$personaId || !is_string($personaId)) {
            return new JsonResponse(['error' => 'Falta persona (UUID)'], 400);
        }

        /** @var Personales|null $persona */
        $persona = $this->personalesRepo->find($personaId);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }

        // Buscar si ya existe registro de recursos para esta persona
        $recursos = $this->recursosRepo->findOneBy(['persona' => $persona]);
        $now = new DateTimeImmutable();

        $created = false;
        if (!$recursos) {
            $recursos = (new PersonalRecursos())
                ->setPersona($persona)
                ->setCreatedAt($now);
            $created = true;
        }

        // Mapear campos permitidos
        if (array_key_exists('vocacion', $payload)) {
            $recursos->setVocacion($payload['vocacion'] !== null ? (string)$payload['vocacion'] : null);
        }
        if (array_key_exists('trabajos', $payload)) {
            $recursos->setTrabajos($payload['trabajos'] !== null ? (string)$payload['trabajos'] : null);
        }
        if (array_key_exists('clases', $payload)) {
            $recursos->setClases($payload['clases'] !== null ? (string)$payload['clases'] : null);
        }
        if (array_key_exists('contribucion', $payload)) {
            $recursos->setContribucion($payload['contribucion'] !== null ? (string)$payload['contribucion'] : null);
        }

        $recursos->setUpdatedAt($now);

        $this->em->persist($recursos);
        $this->em->flush();

        // 201 si se creó, 200 si se actualizó
        $status = $created ? 201 : 200;

        return $this->json($recursos, $status);
    }
}
