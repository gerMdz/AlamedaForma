<?php

namespace App\Controller\Api;

use App\Entity\IntroExtro;
use App\Repository\IntroExtroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/intro-extro')]
class IntroExtroCrudController extends AbstractController
{
    #[Route('', name: 'api_intro_extro_collection', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function collection(Request $request, IntroExtroRepository $repo): JsonResponse
    {
        $activoParam = strtolower((string)($request->query->get('activo', 'true')));
        $qb = $repo->createQueryBuilder('i');
        if ($activoParam === 'true' || $activoParam === '1') {
            $qb->andWhere('i.activo = :act')->setParameter('act', true);
        } elseif ($activoParam === 'false' || $activoParam === '0') {
            $qb->andWhere('i.activo = :act')->setParameter('act', false);
        } else {
            // 'all' u otros valores no filtran por activo
        }
        $items = $qb->orderBy('i.id', 'DESC')->getQuery()->getResult();

        $serializer = $this->serialize();
        return $this->json(array_map($serializer, $items));
    }

    #[Route('', name: 'api_intro_extro_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getJsonData($request);
        $errors = $this->validateData($data, true);
        if ($errors) {
            return $this->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $e = new IntroExtro();
        $e->setIntro((string)$data['intro']);
        $e->setExtro((string)$data['extro']);
        $e->setMitad((string)($data['mitad'] ?? '50/50'));
        $e->setActivo(true);

        $em->persist($e);
        $em->flush();

        return $this->json(($this->serialize())($e), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_intro_extro_get', methods: ['GET'])]
    public function getOne(IntroExtro $introExtro): JsonResponse
    {
        return $this->json(($this->serialize())($introExtro));
    }

    #[Route('/{id}', name: 'api_intro_extro_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, IntroExtro $introExtro, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getJsonData($request);
        $isPut = strtoupper($request->getMethod()) === 'PUT';
        if ($isPut) {
            $errors = $this->validateData($data, true);
            if ($errors) {
                return $this->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if (array_key_exists('intro', $data)) { $introExtro->setIntro((string)$data['intro']); }
        if (array_key_exists('extro', $data)) { $introExtro->setExtro((string)$data['extro']); }
        if (array_key_exists('mitad', $data)) { $introExtro->setMitad((string)$data['mitad']); }
        // Not exposing direct activo change from CRUD UI; delete endpoint will toggle it.

        $em->flush();

        return $this->json(($this->serialize())($introExtro));
    }

    #[Route('/{id}', name: 'api_intro_extro_delete', methods: ['DELETE'])]
    public function softDelete(IntroExtro $introExtro, EntityManagerInterface $em): JsonResponse
    {
        // Soft delete: marcar activo = false
        $introExtro->setActivo(false);
        $em->flush();
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @return array<string,mixed>
     */
    private function getJsonData(Request $request): array
    {
        try {
            if (str_starts_with((string) $request->headers->get('content-type'), 'application/json')) {
                return (array) $request->toArray();
            }
        } catch (\Throwable) {
            // fallthrough
        }
        return [
            'intro' => $request->get('intro'),
            'extro' => $request->get('extro'),
            'mitad' => $request->get('mitad'),
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateData(array $data, bool $requireAll): array
    {
        $errors = [];
        foreach (['intro', 'extro'] as $key) {
            if ($requireAll) {
                if (!isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
                    $errors[$key] = 'Campo obligatorio';
                }
            }
        }
        if (isset($data['mitad']) && $data['mitad'] === '') {
            $errors['mitad'] = 'Campo obligatorio';
        }
        return $errors;
    }

    /**
     * @return callable(IntroExtro): array<string,mixed>
     */
    private function serialize(): callable
    {
        return static function (IntroExtro $e): array {
            return [
                'id' => $e->getId(),
                'intro' => $e->getIntro(),
                'extro' => $e->getExtro(),
                'mitad' => $e->getMitad(),
                'activo' => $e->isActivo(),
            ];
        };
    }
}
