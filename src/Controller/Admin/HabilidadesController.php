<?php

namespace App\Controller\Admin;

use App\Entity\Habilidades;
use App\Repository\HabilidadesRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/admin/habilidades')]
#[IsGranted('ROLE_USER')]
class HabilidadesController extends AbstractController
{
    #[Route('/', name: 'app_habilidades_index', methods: ['GET'])]
    public function index(HabilidadesRepository $repo): Response
    {
        $items = $repo->createQueryBuilder('h')
            ->orderBy('h.nombre', 'ASC')
            ->getQuery()->getResult();

        $data = array_map(function (Habilidades $h) {
            return [
                'id' => (string) $h->getId(),
                'nombre' => $h->getNombre(),
                'discripcion' => $h->getDiscripcion(),
                'identificador' => $h->getIdentificador(),
                'deletedAt' => $h->getDeletedAt() ? $h->getDeletedAt()->format(DATE_ATOM) : null,
                'isDeleted' => $h->isDeleted(),
            ];
        }, $items);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'app_habilidades_show', methods: ['GET'])]
    public function show(string $id, HabilidadesRepository $repo): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var Habilidades|null $item */
        $item = $repo->find($uuid);
        if (!$item || $item->isDeleted()) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => (string) $item->getId(),
            'nombre' => $item->getNombre(),
            'discripcion' => $item->getDiscripcion(),
            'identificador' => $item->getIdentificador(),
        ]);
    }

    #[Route('/', name: 'app_habilidades_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (!isset($data['nombre'], $data['discripcion'], $data['identificador'])) {
            return $this->json(['error' => 'Missing required fields: nombre, discripcion, identificador'], Response::HTTP_BAD_REQUEST);
        }

        $item = (new Habilidades())
            ->setNombre((string) $data['nombre'])
            ->setDiscripcion((string) $data['discripcion'])
            ->setIdentificador((string) $data['identificador']);

        try {
            $em->persist($item);
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Cannot create item: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json([
            'id' => (string) $item->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_habilidades_update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, HabilidadesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var Habilidades|null $item */
        $item = $repo->find($uuid);
        if (!$item || $item->isDeleted()) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (array_key_exists('nombre', $data)) {
            $item->setNombre((string) $data['nombre']);
        }
        if (array_key_exists('discripcion', $data)) {
            $item->setDiscripcion((string) $data['discripcion']);
        }
        if (array_key_exists('identificador', $data)) {
            $item->setIdentificador((string) $data['identificador']);
        }

        try {
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Cannot update item: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(['success' => true]);
    }

    #[Route('/{id}', name: 'app_habilidades_delete', methods: ['DELETE'])]
    public function delete(string $id, HabilidadesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var Habilidades|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$item->isDeleted()) {
            $item->setDeletedAt(new DateTimeImmutable());
            $em->flush();
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/restore', name: 'app_habilidades_restore', methods: ['PUT', 'PATCH'])]
    public function restore(string $id, HabilidadesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var Habilidades|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        if ($item->isDeleted()) {
            $item->setDeletedAt(null);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }
}
