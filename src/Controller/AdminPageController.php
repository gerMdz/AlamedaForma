<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPageController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_page', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login_page');
        }
        return $this->render('admin/index.html.twig');
    }
}
