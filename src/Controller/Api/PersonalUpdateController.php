<?php

namespace App\Controller\Api;

use App\Entity\Personales;
use App\Repository\PersonalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PersonalUpdateController extends AbstractController
{
    public function __construct(private readonly PersonalesRepository $repo)
    {
    }

    #[Route('/api/personal/{id}', name: 'api_personal_update', methods: ['PATCH','PUT'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function update(string $id, Request $request): JsonResponse
    {
        /** @var Personales|null $persona */
        $persona = $this->repo->find($id);
        if (!$persona) {
            return new JsonResponse(['error' => 'Persona no encontrada'], 404);
        }

        $payload = json_decode((string)$request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'JSON inválido'], 400);
        }

        // If changing email+phone, ensure unique constraint is respected
        $newEmail = isset($payload['email']) ? trim((string)$payload['email']) : $persona->getEmail();
        $newPhone = isset($payload['phone']) ? trim((string)$payload['phone']) : ($persona->getPhone() ?? '');
        if ($newEmail !== null && $newEmail !== '' && $newPhone !== null && $newPhone !== '') {
            $existing = $this->repo->findOneByEmailPhone($newEmail, $newPhone);
            if ($existing && $existing->getId() !== $persona->getId()) {
                return new JsonResponse(['error' => 'Ya existe una persona con ese email y teléfono'], 409);
            }
        }

        if (isset($payload['nombre'])) $persona->setNombre((string)$payload['nombre']);
        if (isset($payload['apellido'])) $persona->setApellido((string)$payload['apellido']);
        if (isset($payload['email'])) $persona->setEmail((string)$payload['email']);
        if (array_key_exists('phone', $payload)) $persona->setPhone($payload['phone'] !== null ? (string)$payload['phone'] : null);
        if (array_key_exists('point', $payload)) $persona->setPoint($payload['point'] !== null ? (string)$payload['point'] : null);

        $this->repo->save($persona);

        return $this->json($persona, 200);
    }
}
