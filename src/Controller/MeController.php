<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MeController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'id' => method_exists($user, 'getId') ? $user->getId() : null,
            'email' => method_exists($user, 'getEmail') ? $user->getEmail() : $user->getUserIdentifier(),
            'name' => method_exists($user, 'getName') ? $user->getName() : null,
            'roles' => method_exists($user, 'getRoles') ? $user->getRoles() : [],
        ]);
    }
}
