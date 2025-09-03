<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Personales;
use App\Repository\PersonalesRepository;

class PersonalStateProvider implements ProviderInterface
{
    public function __construct(private readonly PersonalesRepository $repo)
    {
    }

    /**
     * Return a collection with the active/last created Personales.
     * Api Platform will serialize this as a collection (Hydra member[]).
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        // Fetch the most recently created/inserted persona (by id desc). If none, return empty array.
        $result = $this->repo->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }
        // Ensure only Personales instances are returned (defensive)
        return array_values(array_filter($result, static fn($e) => $e instanceof Personales));
    }
}
