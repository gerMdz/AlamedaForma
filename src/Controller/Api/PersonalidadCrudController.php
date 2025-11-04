<?php

namespace App\Controller\Api;

use App\Entity\Personalidad;
use App\Repository\PersonalidadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/personalidad')]
#[IsGranted('ROLE_USER')]
class PersonalidadCrudController extends AbstractController
{
    #[Route('', name: 'api_personalidad_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getJsonData($request);
        $errors = $this->validateData($data);
        if ($errors) {
            return $this->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entity = new Personalidad();
        $entity->setD((string)$data['D']);
        $entity->setI((string)$data['I']);
        $entity->setS((string)$data['S']);
        $entity->setC((string)$data['C']);

        $em->persist($entity);
        $em->flush();

        return $this->json($this->serializeEntity($entity), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_personalidad_get', methods: ['GET'])]
    public function getOne(Personalidad $personalidad): JsonResponse
    {
        return $this->json($this->serializeEntity($personalidad));
    }

    #[Route('/{id}', name: 'api_personalidad_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Personalidad $personalidad, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getJsonData($request);
        // Allow partial for PATCH; require all for PUT
        $isPut = strtoupper($request->getMethod()) === 'PUT';
        if ($isPut) {
            $errors = $this->validateData($data);
            if ($errors) {
                return $this->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if (array_key_exists('D', $data)) { $personalidad->setD((string)$data['D']); }
        if (array_key_exists('I', $data)) { $personalidad->setI((string)$data['I']); }
        if (array_key_exists('S', $data)) { $personalidad->setS((string)$data['S']); }
        if (array_key_exists('C', $data)) { $personalidad->setC((string)$data['C']); }

        $em->flush();

        return $this->json($this->serializeEntity($personalidad));
    }

    #[Route('/{id}', name: 'api_personalidad_delete', methods: ['DELETE'])]
    public function delete(Personalidad $personalidad, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($personalidad);
        $em->flush();
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @return array<string,mixed>
     */
    private function getJsonData(Request $request): array
    {
        try {
            // Support JSON and form-encoded
            if (str_starts_with((string)$request->headers->get('content-type'), 'application/json')) {
                return (array) $request->toArray();
            }
        } catch (\Throwable) {
            // ignore fallthrough
        }
        return [
            'D' => $request->get('D'),
            'I' => $request->get('I'),
            'S' => $request->get('S'),
            'C' => $request->get('C'),
        ];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateData(array $data): array
    {
        $errors = [];
        foreach (['D','I','S','C'] as $key) {
            if (!isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
                $errors[$key] = 'Campo obligatorio';
            }
        }
        return $errors;
    }

    /**
     * @return array<string,mixed>
     */
    private function serializeEntity(Personalidad $e): array
    {
        return [
            'id' => $e->getId(),
            'D' => $e->getD(),
            'I' => $e->getI(),
            'S' => $e->getS(),
            'C' => $e->getC(),
        ];
    }
}
