<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\DTO\DonesDataTransformer;
use App\DTO\FormationDataTransformer;
use App\Entity\Dones;
use App\Entity\Formacion;
use App\Entity\Inicio;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DonesStateProvider implements ProviderInterface
{

    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private DonesDataTransformer                                          $donesDataTransformer,
        private ResourceMetadataCollectionFactoryInterface                        $collectionFactory
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{entity: string} $extraProperties */
        $extraProperties = $operation->getExtraProperties();

        $collection = $this->collectionFactory->create(Dones::class)->getOperation(forceCollection: true)->withOrder(['name' => 'ASC']);

        /** @var Paginator $paginator */
        $paginator = $this->collectionProvider->provide($collection, $uriVariables, $context);
        /** @var array<Dones> $dones */
        $dones = iterator_to_array($paginator, false);

        return array_map($this->donesDataTransformer->transform(...), $dones);
    }
}
