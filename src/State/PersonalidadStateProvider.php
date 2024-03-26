<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use App\DTO\PersonalidadDataTransformer;
use App\Entity\Personalidad;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PersonalidadStateProvider implements ProviderInterface
{

    public function __construct(

        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private PersonalidadDataTransformer $personalidadDataTransformer,
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
        /** @var array<Personalidad> $personalidades */
        $personalidades = iterator_to_array($paginator, false);

        return array_map($this->personalidadDataTransformer->transform(...), $personalidades);
    }
}
