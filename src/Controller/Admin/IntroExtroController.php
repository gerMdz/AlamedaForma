<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/intro-extro')]
#[IsGranted('ROLE_USER')]
class IntroExtroController extends AbstractController
{
    #[Route('/', name: 'app_intro_extro_index', methods: ['GET'])]
    public function index(): Response
    {
        // UI de administración basada en Vue (hash routing)
        return $this->redirect('/admin#intro-extro');
    }
}
