<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Component as InfolistComponent;

abstract class TypeOption
{
    protected ?Closure $modifyComponentCloser = null;
    protected mixed $modifyDefault = null;

    final public static function __(string $key): string
    {
        return __('filament-package_ffhs_custom_forms::type_options.' . $key);
    }

    public static function make(): static
    {
        return new static();
    }

    abstract public function modifyFormComponent(Component $component, mixed $value): Component;

    abstract public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent;

    abstract public function getComponent(string $name): Component;

    abstract public function getDefaultValue(): mixed;

    public function modifyOptionComponent(Closure $closure): static
    {
        $this->modifyComponentCloser = $closure;
        return $this;
    }

    public function modifyDefault(mixed $closure): static
    {
        $this->modifyDefault = $closure;
        return $this;
    }

    public function getModifyOptionComponent(string $name): Component
    {
        if (is_null($this->modifyComponentCloser)) {
            return $this->getComponent($name);
        }
        return ($this->modifyComponentCloser)($this->getComponent($name));
    }

    public function getModifyDefault(): mixed
    {
        $default = $this->getDefaultValue();
        if (is_null($this->modifyDefault)) {
            return $default;
        }
        $isClosure = $this->modifyDefault instanceof Closure;
        return $isClosure
            ? ($this->modifyDefault)($default)
            : $this->modifyDefault;
    }


    //ToDo for GeneralField
    public function mutateOnFieldSave(mixed $data, string $key, CustomField $field): mixed
    {
        return $data;
    }

    public function mutateOnFieldLoad(mixed $data, string $key, CustomField $field): mixed
    {
        return $data;
    }

    public function beforeSaveField(mixed &$data, string $key, CustomField $field): void
    {
    }

    public function afterSaveField(mixed &$data, string $key, CustomField $field): void
    {
    }

    public function afterCreateField(mixed &$data, string $key, CustomField $field): void
    {
    }

    public function afterDeleteField(int|string $key, CustomField $field): void
    {
    }

    public function beforeDeleteField(int|string $key, CustomField $field): void
    {
    }

    public function mutateOnFieldClone(mixed &$data, int|string $key, CustomField $original): mixed
    {
        return $data;
    }

    public function canBeOverwrittenByNonField(): bool
    {
        return true;
    }
}
