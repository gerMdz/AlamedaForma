<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginPageController extends AbstractController
{
    // GET /login will render the login page (POST /login is handled by Security json_login)
    #[Route('/login', name: 'app_login_page', methods: ['GET'])]
    public function loginPage(): Response
    {
        // If already authenticated, skip login form and go to admin
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin_page');
        }

        return $this->render('security/login.html.twig');
    }
}
