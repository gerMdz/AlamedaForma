<?php

namespace App\DTO;

use App\Entity\Formacion;
use App\Model\FormationModel;

class FormationDataTransformer
{
    public function transform(Formacion $value): FormationModel
    {

        $output = new FormationModel();
        $output->id = $value->getId();
        $output->orden =  $value->getOrden();
        $output->description = $value->getDescription();
        $output->identifier = $value->getIdentifier();
        $output->parent = $value->getParent();
        $output->don = $value->getDonAssociate()->getIdentifier();

        return $output;
    }
}