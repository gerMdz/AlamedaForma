<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginCheckRedirectController extends AbstractController
{
    // Handle accidental/direct GET navigations. Use a distinct path to avoid confusion with POST /login_check.
    // The POST /login_check remains handled by Security's json_login authenticator (see config/routes/security.yaml).
    #[Route('/login/redirect', name: 'app_login_redirect', methods: ['GET'])]
    public function redirectAfterLoginCheck(): RedirectResponse
    {
        // If user is authenticated, send them to admin dashboard; otherwise back to admin login page.
        return $this->getUser()
            ? $this->redirect('/admin')
            : $this->redirect('/admin/login');
    }
}
