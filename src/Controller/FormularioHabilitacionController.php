<?php

namespace App\Controller;

use App\Entity\FormularioHabilitacion;
use App\Repository\FormularioHabilitacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioHabilitacionController extends AbstractController
{
    public function __construct(private readonly FormularioHabilitacionRepository $repo)
    {
    }

    #[Route('/api/formularios-habilitacion/activo', name: 'api_formularios_habilitacion_activo', methods: ['GET'])]
    public function hasActivo(): Response
    {
        $now = new \DateTimeImmutable();
        $qb = $this->repo->createQueryBuilder('h')
            ->andWhere('h.activoDesde <= :now')
            ->andWhere('(h.activoHasta IS NULL OR h.activoHasta >= :now)')
            ->setMaxResults(50)
            ->setParameter('now', $now)
            ->orderBy('h.activoDesde', 'DESC');

        $items = $qb->getQuery()->getResult();
        $serialized = array_map(function (FormularioHabilitacion $f) {
            return [
                'id' => $f->getId(),
                'nombreFormulario' => $f->getNombreFormulario(),
                'activoDesde' => $f->getActivoDesde()?->format(DATE_ATOM),
                'activoHasta' => $f->getActivoHasta()?->format(DATE_ATOM),
            ];
        }, $items);

        return new JsonResponse([
            'hasActive' => count($items) > 0,
            'count' => count($items),
            'items' => $serialized,
        ]);
    }

    #[Route('/api/formularios-habilitacion/activos', name: 'api_formularios_habilitacion_activos', methods: ['GET'])]
    public function listActivos(): Response
    {
        $now = new \DateTimeImmutable();
        $qb = $this->repo->createQueryBuilder('h')
            ->andWhere('h.activoDesde <= :now')
            ->andWhere('(h.activoHasta IS NULL OR h.activoHasta >= :now)')
            ->setParameter('now', $now)
            ->orderBy('h.activoDesde', 'DESC');

        $items = $qb->getQuery()->getResult();
        $serialized = array_map(function (FormularioHabilitacion $f) {
            return [
                'id' => $f->getId(),
                'nombreFormulario' => $f->getNombreFormulario(),
                'activoDesde' => $f->getActivoDesde()?->format(DATE_ATOM),
                'activoHasta' => $f->getActivoHasta()?->format(DATE_ATOM),
            ];
        }, $items);

        return new JsonResponse($serialized);
    }
}
