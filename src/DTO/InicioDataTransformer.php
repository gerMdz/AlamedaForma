<?php

namespace App\DTO;

use App\Entity\Inicio;
use App\Model\InicioModel;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class InicioDataTransformer
{
    public function transform(Inicio $value): InicioModel
    {

        $output = new InicioModel();
        $output->id = (Uuid::fromString($value->getId()));
        $output->Title =  $value->getTitle();
        $output->Content = $value->getContent();
        $output->Terms = $value->getTerms();
        $output->Organization = $value->getOrganization()->getName();

        return $output;
    }


}