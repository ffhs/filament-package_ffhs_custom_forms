<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;

abstract class TypeOptionPack {
    protected array $typeOptions;
    protected string $name;
    protected ?string $icon;

    public static function make(string $name, array $typeOptions, ?string $icon = null): static {
        return new static($name, $typeOptions, $icon);
    }

    public function __construct(string $name, array $typeOptions, ?string $icon = null) {
        $this->name = $name;
        $this->typeOptions = $typeOptions;
        $this->icon = $icon;
    }

    public function getTypeOptions(): ?array {
        return $this->typeOptions;
    }

    public function setTypeOptions(array $typeOptions): static {
        $this->typeOptions = $typeOptions;
        return  $this;
    }

    public function addTypeOptions($key, TypeOption $typeOption): static {
        $this->typeOptions[$key] = $typeOption;
        return  $this;
    }

    public function meargTypeOptions(array $typeOptions): static {
       foreach ($typeOptions as $typeOption)
           $this->addTypeOptions($typeOption->getKey(), $typeOption);
        return  $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): TypeOptionPack {
        $this->name = $name;
        return  $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): TypeOptionPack {
        $this->icon = $icon;
        return  $this;
    }



}
