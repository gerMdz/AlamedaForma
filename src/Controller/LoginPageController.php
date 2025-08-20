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
        return $this->render('security/login.html.twig');
    }
}
