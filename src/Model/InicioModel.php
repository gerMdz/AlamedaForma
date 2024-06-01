<?php

namespace App\Model;

class InicioModel
{

    public function __construct(
        public ?string $id = null,
        public ?string $Title = null,
        public ?string $Content = null,
        public ?string $Terms = null,
        public ?string $Organization = null
    ) {
    }
}