<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;

abstract class TypeOption {

    public static function make(): static {
        return new static();
    }

    protected ?Closure $modifyComponentCloser = null;
    protected ?Closure $modifyDefault = null;

    public abstract function getDefaultValue(): mixed;
    public abstract function getComponent(string $name): Component;

   /* public function mutateOnCreate(mixed $value, CustomField $field): mixed { //ToDo for GeneralField
        return $value;
    }
    public function mutateOnSave(mixed $value, CustomField $field): mixed { //ToDo for GeneralField
        return $value;
    }
    public function mutateOnLoad(mixed $value, CustomField $field): mixed { //ToDo for GeneralField
        return $value;
    }*/

    public function modifyComponent(Closure $closure): static {
        $this->modifyComponentCloser = $closure;
        return $this;
    }

    public function modifyDefault(Closure $closure): static {
        $this->modifyDefault = $closure;
        return $this;
    }

    public function getModifyComponent(string $name): Component {
        if (is_null($this->modifyComponentCloser)) return $this->getComponent($name);
        return ($this->modifyComponentCloser)($this->getComponent($name));
    }

    public function getModifyDefault(): mixed {
        $default = $this->getDefaultValue();
        if (is_null($this->modifyDefault)) return $default;
        return ($this->modifyDefault)($default);
    }




    //ToDo for GeneralField
    public function mutateOnFieldSave(mixed $data, string $key, CustomField $field): mixed {return  $data;}
    public function mutateOnFieldLoad(mixed $data, string $key, CustomField $field): mixed {return  $data;}
    public function bforeSaveField(mixed &$data, string $key, CustomField $field):void{}
    public function afterSaveField(mixed &$data, string $key, CustomField $field): void {}
    public function afterCreateField(mixed &$data, string $key, CustomField $field): void {}
    public function afterDeleteField(int|string $key, CustomField $field) {}
    public function beforeDeleteField(int|string $key, CustomField $field) {}

}
