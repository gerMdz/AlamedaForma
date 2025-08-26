<?php

namespace App\Controller\Admin;

use App\Entity\FormularioHabilitacion;
use App\Repository\FormularioHabilitacionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/formularios-habilitacion')]
#[IsGranted('ROLE_USER')]
class FormularioHabilitacionAdminController extends AbstractController
{
    public function __construct(
        private readonly FormularioHabilitacionRepository $repo,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'app_admin_form_hab_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $items = $this->repo->findBy([], ['activoDesde' => 'DESC']);
        return new JsonResponse(array_map([$this, 'serialize'], $items));
    }

    #[Route('', name: 'app_admin_form_hab_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->getPayload()->all();

        $nombre = $data['nombreFormulario'] ?? null;
        $desde = $data['activoDesde'] ?? null;
        $hasta = $data['activoHasta'] ?? null; // optional

        if (!$nombre || !$desde) {
            return new JsonResponse(['error' => 'nombreFormulario y activoDesde son requeridos'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $activoDesde = new DateTimeImmutable($desde);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'activoDesde inválido (usar ISO 8601)'], Response::HTTP_BAD_REQUEST);
        }

        $activoHasta = null;
        if ($hasta !== null && $hasta !== '') {
            try { $activoHasta = new DateTimeImmutable($hasta); } catch (\Throwable $e) {
                return new JsonResponse(['error' => 'activoHasta inválido (usar ISO 8601)'], Response::HTTP_BAD_REQUEST);
            }
        }

        $entity = (new FormularioHabilitacion())
            ->setNombreFormulario($nombre)
            ->setActivoDesde($activoDesde)
            ->setActivoHasta($activoHasta);

        // identifier es opcional para compatibilidad; si viene, se setea
        if (array_key_exists('identifier', $data)) {
            $idn = $data['identifier'];
            $entity->setIdentifier($idn !== '' ? $idn : null);
        }

        $this->em->persist($entity);
        $this->em->flush();

        return new JsonResponse($this->serialize($entity), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_admin_form_hab_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            return new JsonResponse(['error' => 'No encontrado'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($this->serialize($entity));
    }

    #[Route('/{id}', name: 'app_admin_form_hab_update', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            return new JsonResponse(['error' => 'No encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getPayload()->all();

        if (array_key_exists('nombreFormulario', $data)) {
            $nombre = $data['nombreFormulario'];
            if (!$nombre) {
                return new JsonResponse(['error' => 'nombreFormulario no puede ser vacío'], Response::HTTP_BAD_REQUEST);
            }
            $entity->setNombreFormulario($nombre);
        }

        if (array_key_exists('activoDesde', $data)) {
            $desde = $data['activoDesde'];
            if (!$desde) {
                return new JsonResponse(['error' => 'activoDesde no puede ser vacío'], Response::HTTP_BAD_REQUEST);
            }
            try { $entity->setActivoDesde(new DateTimeImmutable($desde)); } catch (\Throwable $e) {
                return new JsonResponse(['error' => 'activoDesde inválido (usar ISO 8601)'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (array_key_exists('activoHasta', $data)) {
            $hasta = $data['activoHasta'];
            if ($hasta === null || $hasta === '') {
                $entity->setActivoHasta(null);
            } else {
                try { $entity->setActivoHasta(new DateTimeImmutable($hasta)); } catch (\Throwable $e) {
                    return new JsonResponse(['error' => 'activoHasta inválido (usar ISO 8601)'], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        if (array_key_exists('identifier', $data)) {
            $idn = $data['identifier'];
            $entity->setIdentifier($idn !== '' ? $idn : null);
        }

        $this->em->flush();

        return new JsonResponse($this->serialize($entity));
    }

    // Deshabilitar: setea activoHasta a ahora
    #[Route('/{id}/deshabilitar', name: 'app_admin_form_hab_disable', methods: ['POST'])]
    public function disable(string $id): JsonResponse
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            return new JsonResponse(['error' => 'No encontrado'], Response::HTTP_NOT_FOUND);
        }
        $entity->setActivoHasta(new DateTimeImmutable());
        $this->em->flush();

        return new JsonResponse($this->serialize($entity));
    }

    private function serialize(FormularioHabilitacion $f): array
    {
        return [
            'id' => $f->getId(),
            'identifier' => $f->getIdentifier(),
            'nombreFormulario' => $f->getNombreFormulario(),
            'activoDesde' => $f->getActivoDesde()?->format(DATE_ATOM),
            'activoHasta' => $f->getActivoHasta()?->format(DATE_ATOM),
            'estaActivo' => $f->isActivo(),
        ];
    }
}
