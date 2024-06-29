<?php

namespace App\Controller\Api;

use App\DTO\PersonalesDataTransformer;
use App\Repository\PersonalesRepository;
use App\Repository\PersonalidadRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonalController extends AbstractController
{


    public function __construct(private readonly PersonalesRepository $personalesRepository)
    {
    }

    public function __invoke(Request $request): Response
    {

        $data = $request->getContent();

        if (empty($data)) {
            return $this->json(['error' => 'No Se recibieron datos'], 400);
        }

        $data = new PersonalesDataTransformer(json_decode($data, true));

        $data = $data->transform();

        $persona = $this->personalesRepository->guardar($data);

        return $this->json( $persona, 200);
    }
}