<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\DTO\InicioDataTransformer;
use ApiPlatform\Doctrine\Orm\Paginator;
use App\Entity\Inicio;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class InicioStateProvider implements ProviderInterface
{

    public function __construct(

        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private InicioDataTransformer                                             $inicioDataTransformer,
        private ResourceMetadataCollectionFactoryInterface                        $collectionFactory
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
        /** @var array<Inicio> $inicio */
        $inicio = iterator_to_array($paginator, false);

        return array_map($this->inicioDataTransformer->transform(...), $inicio);
    }
}
