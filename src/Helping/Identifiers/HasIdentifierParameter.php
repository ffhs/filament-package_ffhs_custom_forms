<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers;

trait HasIdentifierParameter{
    public function identifier(): string
    {
        return $this->identifier;
    }
}
