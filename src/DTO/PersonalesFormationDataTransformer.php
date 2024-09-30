<?php

namespace App\DTO;

use App\Entity\PersonalFormation;

class PersonalesFormationDataTransformer
{

    public ?string $percentDon = null;
    public ?string $commentDon = null;
    public ?string $don = null;
    public ?string $person = null;

    public function __construct(array $requestData)
    {
        $this->percentDon = $requestData['percentDon'];
        $this->commentDon = $requestData['commentDon'];
        $this->don = $requestData['don'];
        $this->person = $requestData['person'];
    }

    public function transform(): PersonalFormation
    {
        $personal_formation = new PersonalFormation();

        $personal_formation->setPercentDon($this->percentDon);
        $personal_formation->setCommentDon($this->commentDon);
        $personal_formation->setDon($this->don);
        $personal_formation->setPerson($this->person);

        return $personal_formation;
    }
}