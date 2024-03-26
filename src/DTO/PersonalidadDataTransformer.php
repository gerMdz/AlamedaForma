<?php

namespace App\DTO;

use App\Entity\Personalidad;

class PersonalidadDataTransformer
{
    public function transform(Personalidad $value): Personalidad
    {

        $output = new Personalidad();
        $output->id = (int) $value->getId();
        $output->D = $value->getD();
        $output->I = $value->getI();
        $output->S = $value->getS();
        $output->C = $value->getC();


        return $output;
    }


}