<?php

namespace App\Controller\Api;

use App\Repository\InicioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/inicios')]
class InicioPublicController extends AbstractController
{
    // Public list of all Inicio objects with links to view and edit (admin URLs)
    #[Route('', name: 'api_inicio_list', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function listPublic(InicioRepository $inicioRepository, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $items = [];
        foreach ($inicioRepository->findAll() as $inicio) {
            $id = $inicio->getId();
            $items[] = [
                'id' => $id,
                'links' => [
                    'show' => $urlGenerator->generate('app_admin_inicio_show', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL),
                    'edit' => $urlGenerator->generate('app_admin_inicio_edit', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ];
        }

        return new JsonResponse($items);
    }
}
