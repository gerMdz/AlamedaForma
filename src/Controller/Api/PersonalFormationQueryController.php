<?php

namespace App\Controller\Api;

use App\Entity\Personales;
use App\Repository\PersonalFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/personal-formation')]
class PersonalFormationQueryController extends AbstractController
{
    public function __construct(private readonly PersonalFormationRepository $repo)
    {
    }

    #[Route('/ultimos/{personalId}', name: 'api_personal_formation_ultimos', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function ultimos(string $personalId): JsonResponse
    {
        // Find latest 3 for a person; return 404 if personId invalid format or no records? We'll return empty array if none.
        $qb = $this->repo->createQueryBuilder('pf')
            ->andWhere('pf.person = :pid')
            ->setParameter('pid', $personalId)
            ->orderBy('pf.createdAt', 'DESC')
            ->setMaxResults(3);
        $res = $qb->getQuery()->getResult();

        return $this->json($res, 200);
    }
}
