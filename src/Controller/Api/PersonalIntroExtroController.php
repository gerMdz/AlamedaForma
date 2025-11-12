<?php

namespace App\Controller\Api;

use App\Entity\PersonalIntroExtro;
use App\Entity\Personales;
use App\Repository\PersonalIntroExtroRepository;
use App\Repository\PersonalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Throwable;

#[Route('/api/personal-intro-extro')]
class PersonalIntroExtroController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PersonalesRepository $personalesRepo,
        private readonly PersonalIntroExtroRepository $pieRepo,
    ) {
    }

    /**
     * Crea un registro que vincula un Personales con un snapshot JSON de Intro/Extro.
     *
     * Request body esperado (JSON o x-www-form-urlencoded):
     * - personId: string UUID (v7 recomendado) del Personales
     * - introExtro: objeto/array con los datos del formulario Intro/Extro
     */
    #[Route('', name: 'api_personal_intro_extro_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        [$data, $contentType] = $this->readRequestPayload($request);

        $personId = $data['personId'] ?? null;
        $introExtro = $data['introExtro'] ?? null;

        if (!$personId || !is_string($personId)) {
            return $this->error('El campo personId es obligatorio y debe ser string UUID.', Response::HTTP_BAD_REQUEST, [
                'field' => 'personId',
            ]);
        }

        if (!Uuid::isValid($personId)) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, [
                'field' => 'personId',
                'value' => $personId,
            ]);
        }

        if ($introExtro === null) {
            return $this->error('El campo introExtro es obligatorio.', Response::HTTP_BAD_REQUEST, [
                'field' => 'introExtro',
            ]);
        }

        // Aceptamos objeto JSON o array. Si viene como string, intentamos decodificarlo.
        if (is_string($introExtro)) {
            $decoded = json_decode($introExtro, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $introExtro = $decoded;
            }
        }

        if (!is_array($introExtro)) {
            return $this->error('El campo introExtro debe ser un objeto/array.', Response::HTTP_BAD_REQUEST, [
                'field' => 'introExtro',
                'givenType' => gettype($introExtro),
            ]);
        }

        // Buscar la persona
        try {
            $uuid = Uuid::fromString($personId);
        } catch (Throwable $e) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, [
                'field' => 'personId',
                'value' => $personId,
            ]);
        }

        /** @var Personales|null $person */
        $person = $this->personalesRepo->find($uuid);
        if (!$person) {
            return $this->error('No se encontró la persona.', Response::HTTP_NOT_FOUND, [
                'personId' => $personId,
            ]);
        }

        // Persistir el snapshot
        $entity = new PersonalIntroExtro();
        $entity->setPerson($person);
        $entity->setIntroExtroData($introExtro);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->json([
            'id' => (string) $entity->getId(),
            'personId' => (string) $person->getId(),
            'createdAt' => $entity->getCreatedAt()->format(DATE_ATOM),
            'introExtro' => $entity->getIntroExtroData(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Lista los snapshots por persona, ordenados por creación descendente.
     */
    #[Route('/by-person/{personId}', name: 'api_personal_intro_extro_by_person', methods: ['GET'])]
    public function listByPerson(string $personId): JsonResponse
    {
        if (!Uuid::isValid($personId)) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, [
                'field' => 'personId',
                'value' => $personId,
            ]);
        }
        $uuid = Uuid::fromString($personId);

        /** @var Personales|null $person */
        $person = $this->personalesRepo->find($uuid);
        if (!$person) {
            return $this->error('No se encontró la persona.', Response::HTTP_NOT_FOUND, [
                'personId' => $personId,
            ]);
        }

        $items = $this->pieRepo->createQueryBuilder('p')
            ->andWhere('p.person = :person')
            ->setParameter('person', $person)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $result = array_map(static function (PersonalIntroExtro $e) {
            return [
                'id' => (string) $e->getId(),
                'createdAt' => $e->getCreatedAt()->format(DATE_ATOM),
                'introExtro' => $e->getIntroExtroData(),
            ];
        }, $items);

        return $this->json([
            'personId' => (string) $person->getId(),
            'count' => count($result),
            'items' => $result,
        ]);
    }

    private function readRequestPayload(Request $request): array
    {
        $contentType = $request->headers->get('Content-Type', '');

        // JSON body
        if (str_contains($contentType, 'application/json')) {
            $raw = $request->getContent() ?: '';
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $data = [];
            }
            return [$data, 'json'];
        }

        // application/x-www-form-urlencoded or multipart/form-data
        if ($request->request->count() > 0) {
            return [$request->request->all(), 'form'];
        }

        // Try to parse as JSON anyway if content-type is missing
        $raw = $request->getContent() ?: '';
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = [];
        }
        return [$data, 'json'];
    }

    private function error(string $message, int $status, array $details = []): JsonResponse
    {
        return $this->json([
            'error' => [
                'message' => $message,
                'details' => $details,
                'status' => $status,
            ]
        ], $status);
    }
}
