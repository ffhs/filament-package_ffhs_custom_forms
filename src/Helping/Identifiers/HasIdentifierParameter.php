<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers;

trait HasIdentifierParameter{
    public $identifier;

    public function identifier(): string
    {
        return $this->identifier;
    }
}
