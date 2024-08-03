<?php

namespace App\DTO;

use App\Entity\Dones;
use App\Entity\Formacion;
use App\Model\DonesModel;
use App\Model\FormationModel;

class DonesDataTransformer
{
    public function transform(Dones $value): DonesModel
    {

        $output = new DonesModel();
        $output->id = $value->getId();
        $output->name = $value->getName();
        $output->description = $value->getDescription();
        $output->identifier = $value->getIdentifier();

        return $output;
    }
}