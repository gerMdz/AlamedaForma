<?php

namespace App\Model;

class InstructionModel
{

    public function __construct(
        public ?string $id = null,
        public ?string $Title = null,
        public ?string $Content = null,
        public ?bool $Enabled = null,
        public ?string $Organization = null
    ) {
    }
}