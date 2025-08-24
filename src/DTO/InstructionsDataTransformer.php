<?php

namespace App\DTO;

use App\Entity\Instructions;
use App\Model\InstructionModel;

class InstructionsDataTransformer
{
    public function transform(Instructions $value): InstructionModel
    {
        $output = new InstructionModel();
        // Ensure ID is serialized as string
        $output->id = (string) $value->getId();
        $output->Title = $value->getTitle();
        $output->Content = $value->getContent();
        // Organization may be null; avoid errors
        $output->Organization = $value->getOrganization()?->getName();
        // Enabled may be missing in model; set it if available on entity
        $output->Enabled = $value->isEnabled();

        return $output;
    }
}