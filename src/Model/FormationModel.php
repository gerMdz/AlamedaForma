<?php

namespace App\Model;

class FormationModel
{
    public function __construct(
        public ?string $id = null,
        public ?string $orden = null,
        public ?string $description = null,
        public ?string $identifier = null,
        public ?string $parent = null,
        public ?string $don = null
    ) {
    }
}