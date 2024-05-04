<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\DTO\InicioDataTransformer;
use App\DTO\PersonalidadDataTransformer;
use ApiPlatform\Doctrine\Orm\Paginator;
use App\Entity\Inicio;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class InicioStateProvider implements ProviderInterface
{

    public function __construct(

        #[Autowire(service: CollectionProvider::class)] private readonly ProviderInterface $collectionProvider,
        private readonly InicioDataTransformer                                             $inicioDataTransformer,
        private readonly ResourceMetadataCollectionFactoryInterface                        $collectionFactory
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var array{entity: string} $extraProperties */
        $extraProperties = $operation->getExtraProperties();
//        $collection = $this->collectionFactory->create($extraProperties['entity'])->getOperation(forceCollection: true);


        $data = [
            'id' => 'a335144b-b46c-4b50-b514-4bb46ccb50d9',
            'title' => 'Descubre tu propósito, conociendo tu F.O.R.M.A',
            'content' => 'Con el propósito de brindar una orientación a las personas que desean servir en cualquiera de los Ministerios de Iglesia Evangélica Vida Real, hemos desarrollado una herramienta que te ayudará a conocer tu diseño dado por Dios, es decir tu F.O.R.M.A; cuyo acróstico representa 5 áreas, siendo: 1. Formación Espiritual, 2. Orientación del Corazón, 3. Recursos y Habilidades, 4. Mi Personalidad y 5. Antecedentes. Esta herramienta te brindará una orientación ministerial, que te ayudará a expresar de mejor manera el diseño que Dios puso en ti; nuestro objetivo es que cada persona sirva con amor, siguiendo el modelo de Jesús, sabiendo que las capacidades las da Él y por último, contar con la actitud correcta; el resultado de esto te ayudará a descubrir el propósito de su vida.
Previo a realizar este test, le solicitamos proceda a leer los Términos y Condiciones; luego de haber finalizado con la lectura y si se encuentra de acuerdo, favor dar clic en ACEPTO LOS TÉRMINOS Y CONDICIONES.',
            'terms' => 'Acepto esto'
        ];
        $collection = $data;

        /** @var Paginator $paginator */
//        $paginator = $this->collectionProvider->provide($collection, $uriVariables, $context);

        /** @var array<Inicio> $inicios */

        $inicios = iterator_to_array($collection, false);
//        $inicios = iterator_to_array($paginator, false);





//        return array_map($this->inicioDataTransformer->transform(...), $inicios);
        return $data;

    }
}
