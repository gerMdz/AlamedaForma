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
        $raw = $request->getContent();
        if (empty($raw)) {
            return $this->json(['error' => 'No Se recibieron datos'], 400);
        }

        $input = json_decode($raw, true);
        if (!is_array($input)) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        // Buscar existente por email+phone (email case-insensitive)
        $email = isset($input['email']) ? trim((string)$input['email']) : '';
        $phone = isset($input['phone']) ? trim((string)$input['phone']) : '';
        if ($email !== '' && $phone !== '') {
            $existing = $this->personalesRepository->findOneByEmailPhone($email, $phone);
            if ($existing) {
                // 200 OK: ya existía. Frontend puede mostrar aviso y continuar.
                return $this->json($existing, 200);
            }
        }

        // Crear nuevo si no existía
        $transformer = new PersonalesDataTransformer($input);
        $entity = $transformer->transform();
        $persona = $this->personalesRepository->guardar($entity);

        return $this->json($persona, 201);
    }
}