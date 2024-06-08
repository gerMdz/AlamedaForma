<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use App\DTO\InstructionsDataTransformer;
use App\DTO\PersonalidadDataTransformer;
use App\Entity\Instructions;
use App\Entity\Personalidad;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class InstructionsStateProvider implements ProviderInterface
{

    public function __construct(

        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private InstructionsDataTransformer $instructionsDataTransformer,
        private ResourceMetadataCollectionFactoryInterface $collectionFactory
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{entity: string} $extraProperties */
        $extraProperties = $operation->getExtraProperties();
        $collection = $this->collectionFactory->create($extraProperties['entity'])->getOperation(forceCollection: true);

        /** @var Paginator $paginator */
        $paginator = $this->collectionProvider->provide($collection, $uriVariables, $context);
        /** @var array<Instructions> $instructions */
        $instructions = iterator_to_array($paginator, false);

        return array_map($this->instructionsDataTransformer->transform(...), $instructions);
    }
}
