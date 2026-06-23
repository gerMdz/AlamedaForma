<?php

namespace App\Controller\Admin;

use App\Entity\Antecedentes;
use App\Repository\AntecedentesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/admin/antecedentes')]
#[IsGranted('ROLE_USER')]
class AntecedentesController extends AbstractController
{
    #[Route('/', name: 'app_antecedentes_index', methods: ['GET'])]
    public function index(AntecedentesRepository $repo): Response
    {
        $items = $repo->createQueryBuilder('a')
            ->orderBy('a.label', 'ASC')
            ->getQuery()->getResult();

        $data = array_map(function (Antecedentes $a) {
            return [
                'id' => (string) $a->getId(),
                'activo' => $a->isActivo(),
                'label' => $a->getLabel(),
                'consulta' => $a->getConsulta(),
                'esTitulo' => $a->isEsTitulo(),
            ];
        }, $items);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'app_antecedentes_show', methods: ['GET'])]
    public function show(string $id, AntecedentesRepository $repo): JsonResponse
    {
        try {
            $uuid = Uuid::fromRfc4122($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Antecedentes|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => (string) $item->getId(),
            'activo' => $item->isActivo(),
            'label' => $item->getLabel(),
            'consulta' => $item->getConsulta(),
            'esTitulo' => $item->isEsTitulo(),
        ]);
    }

    #[Route('/', name: 'app_antecedentes_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (!isset($data['label'], $data['consulta'])) {
            return $this->json(['error' => 'Missing required fields: label, consulta'], Response::HTTP_BAD_REQUEST);
        }

        $item = (new Antecedentes())
            ->setLabel((string) $data['label'])
            ->setConsulta((string) $data['consulta'])
            ->setActivo((bool) ($data['activo'] ?? true))
            ->setEsTitulo((bool) ($data['esTitulo'] ?? false));

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

    #[Route('/{id}', name: 'app_antecedentes_update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, AntecedentesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        try {
            $uuid = Uuid::fromRfc4122($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Antecedentes|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent() ?: '[]', true) ?? [];
        if (array_key_exists('label', $data)) {
            $item->setLabel((string) $data['label']);
        }
        if (array_key_exists('consulta', $data)) {
            $item->setConsulta((string) $data['consulta']);
        }
        if (array_key_exists('activo', $data)) {
            $item->setActivo((bool) $data['activo']);
        }
        if (array_key_exists('esTitulo', $data)) {
            $item->setEsTitulo((bool) $data['esTitulo']);
        }

        try {
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Cannot update item: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(['success' => true]);
    }

    #[Route('/{id}', name: 'app_antecedentes_delete', methods: ['DELETE'])]
    public function delete(string $id, AntecedentesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        try {
            $uuid = Uuid::fromRfc4122($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Antecedentes|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($item);
            $em->flush();
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Cannot delete item: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/toggle-activo', name: 'app_antecedentes_toggle_activo', methods: ['PUT', 'PATCH'])]
    public function toggleActivo(string $id, AntecedentesRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        try {
            $uuid = Uuid::fromRfc4122($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Antecedentes|null $item */
        $item = $repo->find($uuid);
        if (!$item) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $item->setActivo(!$item->isActivo());
        $em->flush();

        return $this->json(['success' => true, 'activo' => $item->isActivo()]);
    }
}
