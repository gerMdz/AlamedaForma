<?php

namespace App\Controller\Api;

use App\Repository\PersonalesRepository;
use App\Repository\PersonalidadRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonalController extends AbstractController
{



    public function __construct(private readonly PersonalesRepository $personalesRepository)
    {
    }

    public function __invoke(): Response
    {
        dd('234');
        return $this->personalesRepository->findAll();
    }
}