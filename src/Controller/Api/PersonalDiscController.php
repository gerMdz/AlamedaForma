<?php

namespace App\Controller\Api;

use App\Entity\PersonalDisc;
use App\Entity\Personales;
use App\Repository\PersonalDiscRepository;
use App\Repository\PersonalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/personal-disc')]
class PersonalDiscController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PersonalesRepository $personalesRepo,
        private readonly PersonalDiscRepository $discRepo,
    ) {
    }

    /**
     * Crea o actualiza el registro DISC de una persona (1 por persona).
     *
     * Body esperado (application/json o form-data):
     * {
     *   "personId": "<uuid>",
     *   "d": <int>,
     *   "i": <int>,
     *   "s": <int>,
     *   "c": <int>
     * }
     */
    #[Route('', name: 'api_personal_disc_create', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function create(Request $request): JsonResponse
    {
        [$data] = $this->readRequestPayload($request);

        $personId = $data['personId'] ?? null;
        $d = $data['d'] ?? null;
        $i = $data['i'] ?? null;
        $s = $data['s'] ?? null;
        $c = $data['c'] ?? null;

        // Validaciones básicas
        if (!$personId || !is_string($personId)) {
            return $this->error('El campo personId es obligatorio y debe ser string UUID.', Response::HTTP_BAD_REQUEST, ['field' => 'personId']);
        }
        if (!Uuid::isValid($personId)) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, ['field' => 'personId', 'value' => $personId]);
        }
        foreach (['d' => $d, 'i' => $i, 's' => $s, 'c' => $c] as $key => $val) {
            if (!is_int($val)) {
                // Permitir números en string si representan enteros
                if (is_string($val) && preg_match('/^-?\d+$/', $val)) {
                    ${$key} = (int) $val; // cast
                    continue;
                }
                return $this->error("El campo $key es obligatorio y debe ser entero.", Response::HTTP_BAD_REQUEST, ['field' => $key, 'value' => $val]);
            }
        }

        // Buscar persona
        try {
            $uuid = Uuid::fromString($personId);
        } catch (\Throwable) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, ['field' => 'personId', 'value' => $personId]);
        }
        /** @var Personales|null $person */
        $person = $this->personalesRepo->find($uuid);
        if (!$person) {
            return $this->error('No se encontró la persona.', Response::HTTP_NOT_FOUND, ['personId' => $personId]);
        }

        // Upsert: garantizar un único registro por persona
        $entity = $this->discRepo->findOneBy(['person' => $person]);
        $isCreate = false;
        if (!$entity) {
            $entity = new PersonalDisc();
            $entity->setPerson($person);
            $isCreate = true;
        }

        $entity
            ->setD((int) $d)
            ->setI((int) $i)
            ->setS((int) $s)
            ->setC((int) $c);

        if ($isCreate) {
            $this->em->persist($entity);
        }
        $this->em->flush();

        return $this->json([
            'id' => (string) $entity->getId(),
            'personId' => (string) $person->getId(),
            'createdAt' => $entity->getCreatedAt()->format(DATE_ATOM),
            'd' => $entity->getD(),
            'i' => $entity->getI(),
            's' => $entity->getS(),
            'c' => $entity->getC(),
            'updated' => !$isCreate,
        ], $isCreate ? Response::HTTP_CREATED : Response::HTTP_OK);
    }

    /**
     * Lista los registros DISC por persona, ordenados por fecha de creación DESC.
     */
    #[Route('/by-person/{personId}', name: 'api_personal_disc_by_person', methods: ['GET'])]
    public function listByPerson(string $personId): JsonResponse
    {
        if (!Uuid::isValid($personId)) {
            return $this->error('personId no es un UUID válido.', Response::HTTP_BAD_REQUEST, ['field' => 'personId', 'value' => $personId]);
        }
        $uuid = Uuid::fromString($personId);

        /** @var Personales|null $person */
        $person = $this->personalesRepo->find($uuid);
        if (!$person) {
            return $this->error('No se encontró la persona.', Response::HTTP_NOT_FOUND, ['personId' => $personId]);
        }

        $items = $this->discRepo->createQueryBuilder('pd')
            ->andWhere('pd.person = :person')
            ->setParameter('person', $person)
            ->orderBy('pd.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $result = array_map(static function (PersonalDisc $e) {
            return [
                'id' => (string) $e->getId(),
                'createdAt' => $e->getCreatedAt()->format(DATE_ATOM),
                'd' => $e->getD(),
                'i' => $e->getI(),
                's' => $e->getS(),
                'c' => $e->getC(),
            ];
        }, $items);

        return $this->json([
            'personId' => $personId,
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
