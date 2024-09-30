<?php

namespace App\Controller\Api;

use App\DTO\PersonalesDataTransformer;
use App\DTO\PersonalesFormationDataTransformer;
use App\Repository\PersonalesRepository;
use App\Repository\PersonalFormationRepository;
use App\Repository\PersonalidadRepository;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonalFormationController extends AbstractController
{


    public function __construct(private readonly PersonalFormationRepository $personalFormationRepository)
    {
    }

    /**
     * @throws JsonException
     */
    public function __invoke(Request $request): Response
    {

        $data = $request->getContent();

        if (empty($data)) {
            return $this->json(['error' => 'No Se recibieron datos'], 400);
        }

        $data = new PersonalesFormationDataTransformer(json_decode($data, true, 512, JSON_THROW_ON_ERROR));

        $data = $data->transform();

        $persona = $this->personalFormationRepository->guardar($data);

        return $this->json( $persona, 201);
    }
}