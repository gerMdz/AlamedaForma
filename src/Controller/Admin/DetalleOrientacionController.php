<?php

namespace App\Controller\Admin;

use App\Entity\DetalleOrientacion;
use App\Repository\DetalleOrientacionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/admin/detalle-orientacion')]
#[IsGranted('ROLE_USER')]
class DetalleOrientacionController extends AbstractController
{
    #[Route('/', name: 'app_detalle_orientacion_index', methods: ['GET'])]
    public function index(DetalleOrientacionRepository $repo): Response
    {
        // Incluir todos (activos y eliminados) y exponer estado
        $items = $repo->createQueryBuilder('d')
            ->orderBy('d.orden', 'ASC')
            ->getQuery()->getResult();
        $data = array_map(function (DetalleOrientacion $d) {
            return [
                'id' => (string) $d->getId(),
                'orden' => $d->getOrden(),
                'descripcion' => $d->getDescripcion(),
                'identifier' => $d->getIdentifier(),
                'deletedAt' => $d->getDeletedAt() ? $d->getDeletedAt()->format(DATE_ATOM) : null,
                'isDeleted' => $d->isDeleted(),
            ];
        }, $items);
        return $this->json($data);
    }

    #[Route('/{id}', name: 'app_detalle_orientacion_show', methods: ['GET'])]
    public function show(string $id, DetalleOrientacionRepository $repo): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var DetalleOrientacion|null $item */
        $item = $repo->find($uuid);
        if (!$item || $item->isDeleted()) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => (string) $item->getId(),
            'orden' => $item->getOrden(),
            'descripcion' => $item->getDescripcion(),
            'identifier' => $item->getIdentifier(),
        ]);
    }

    #[Route('/', name: 'app_detalle_orientacion_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (!isset($data['orden'], $data['descripcion'], $data['identifier'])) {
            return $this->json(['error' => 'Missing required fields: orden, descripcion, identifier'], Response::HTTP_BAD_REQUEST);
        }
        $item = (new DetalleOrientacion())
            ->setOrden((int) $data['orden'])
            ->setDescripcion((string) $data['descripcion'])
            ->setIdentifier((string) $data['identifier']);
        try {
            $em->persist($item);
            $em->flush();
        } catch (\Throwable $e) {
            // likely unique constraint violation on identifier
            return $this->json(['error' => 'Cannot create item: '.$e->getMessage()], Response::HTTP_CONFLICT);
        }
        return $this->json([
            'id' => (string) $item->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_detalle_orientacion_update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, DetalleOrientacionRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var DetalleOrientacion|null $item */
        $item = $repo->find($uuid);
        if (!$item || $item->isDeleted()) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (array_key_exists('orden', $data)) {
            $item->setOrden((int) $data['orden']);
        }
        if (array_key_exists('descripcion', $data)) {
            $item->setDescripcion((string) $data['descripcion']);
        }
        if (array_key_exists('identifier', $data)) {
            $item->setIdentifier((string) $data['identifier']);
        }
        try {
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Cannot update item: '.$e->getMessage()], Response::HTTP_CONFLICT);
        }
        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_detalle_orientacion_delete', methods: ['DELETE'])]
    public function delete(string $id, DetalleOrientacionRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var DetalleOrientacion|null $item */
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

    #[Route('/{id}/restore', name: 'app_detalle_orientacion_restore', methods: ['PUT', 'PATCH'])]
    public function restore(string $id, DetalleOrientacionRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $uuid = Uuid::fromRfc4122($id);
        /** @var DetalleOrientacion|null $item */
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
