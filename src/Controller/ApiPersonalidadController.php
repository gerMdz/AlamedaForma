<?php

namespace App\Controller;

use App\Entity\Personalidad;
use App\Repository\PersonalidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiPersonalidadController extends AbstractController
{
    #[Route('/api/listado', name: 'app_api_personalidad')]
    public function listado(PersonalidadRepository $personalidadRepository): Response
    {
        return $this->json($personalidadRepository->findAll());
    }
}
