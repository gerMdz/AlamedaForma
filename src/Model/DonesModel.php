<?php

namespace App\Model;

class DonesModel
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $identifier = null,
    ) {
    }
}