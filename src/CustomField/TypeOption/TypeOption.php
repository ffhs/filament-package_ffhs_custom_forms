<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;

abstract class TypeOption
{

    protected ?Closure $modifyComponentCloser = null;

    public abstract function getDefaultValue():mixed;
    public abstract function getComponent(string $name):Component;

    public function mutateOnCreate(mixed $value, CustomField $field):mixed{ //ToDo for GeneralField
        return $value;
    }
    public function mutateOnSave(mixed $value, CustomField $field):mixed{ //ToDo for GeneralField
        return $value;
    }
    public function mutateOnLoad(mixed $value, CustomField $field):mixed{ //ToDo for GeneralField
        return $value;
    }

    public function modifyComponent(Closure $closure):static{
        $this->modifyComponentCloser = $closure;
        return $this;
    }

    public function getModifyComponent(string $name):Component{
        if(is_null($this->modifyComponentCloser)) return $this->getComponent($name);
        return ($this->modifyComponentCloser)($this->getComponent($name));
    }



}
