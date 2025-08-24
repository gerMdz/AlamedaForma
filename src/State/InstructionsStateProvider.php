<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\DTO\InstructionsDataTransformer;
use App\Entity\Instructions;
use App\Repository\InstructionsRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class InstructionsStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private InstructionsDataTransformer $instructionsDataTransformer,
        private ResourceMetadataCollectionFactoryInterface $collectionFactory,
        private InstructionsRepository $instructionsRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Handle collection operations (e.g., DTO ApiResource GetCollection)
        if ($operation instanceof GetCollection) {
            $extraProperties = $operation->getExtraProperties() ?? [];
            $entityClass = is_string($extraProperties['entity'] ?? null) ? $extraProperties['entity'] : Instructions::class;

            $collectionOp = $this->collectionFactory->create($entityClass)->getOperation(forceCollection: true);
            /** @var Paginator $paginator */
            $paginator = $this->collectionProvider->provide($collectionOp, $uriVariables, $context);
            /** @var array<Instructions> $instructions */
            $instructions = iterator_to_array($paginator, false);

            return array_map($this->instructionsDataTransformer->transform(...), $instructions);
        }

        // Handle item operation: fetch a single entity by identifier and transform it
        $id = $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        $entity = $this->instructionsRepository->find($id);
        if (!$entity) {
            return null; // Let API Platform convert to 404
        }

        return $this->instructionsDataTransformer->transform($entity);
    }
}
