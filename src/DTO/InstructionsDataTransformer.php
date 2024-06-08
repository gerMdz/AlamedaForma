<?php

namespace App\DTO;

use App\Entity\Instructions;
use App\Model\InstructionModel;
use Symfony\Component\Uid\Uuid;

class InstructionsDataTransformer
{
    public function transform(Instructions $value): InstructionModel
    {

        $output = new InstructionModel();
        $output->id = (Uuid::fromString($value->getId()));
        $output->Title =  $value->getTitle();
        $output->Content = $value->getContent();
        $output->Organization = $value->getOrganization()->getName();


        return $output;
    }


}