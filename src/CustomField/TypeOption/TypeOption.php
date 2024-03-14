<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption;

use Filament\Forms\Components\Component;

abstract class TypeOption
{

    public abstract function getDefaultValue():mixed;
    public abstract function getComponent():Component;
    public function prepareBeforeSave(mixed $value):mixed{
        return $value;
    }
    public function prepareBeforeLoad(mixed $value):mixed{
        return $value;
    }

    public function prepareOnClone(mixed $value):mixed{
        return $value;
    }



}
