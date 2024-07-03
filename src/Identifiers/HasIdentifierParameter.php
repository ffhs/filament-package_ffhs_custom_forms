<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Identifiers;

trait HasIdentifierParameter{
    public $identifier;

    public function identifier(): string
    {
        return $this->identifier;
    }
}
