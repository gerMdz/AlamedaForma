<?php

namespace App\DTO;

use App\Entity\Inicio;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class InicioDataTransformer
{
    public function transform(Inicio $value): Inicio
    {

        $output = new Inicio();
        $output->id = (string) $value->getId()->toRfc4122();
        $output->Title = $value->getTitle();
        $output->Content = $value->getContent();
        $output->Terms = $value->getTerms();
        $output->Organization = $value->getOrganization();


        return $output;
    }


}