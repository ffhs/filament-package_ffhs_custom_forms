<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption;

use Filament\Forms\Components\Section;

class TypeOptionGroup {
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

    public function mergeTypeOptions(array $typeOptions): static {
       foreach ($typeOptions as $key => $typeOption)
           $this->addTypeOptions($key, $typeOption);
        return  $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): TypeOptionGroup {
        $this->name = $name;
        return  $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): TypeOptionGroup {
        $this->icon = $icon;
        return  $this;
    }

    public function getModifyComponent(): Section {
        $data = [];
        foreach ($this->getTypeOptions() as $key => $extraTypeOption) {
            /**@var TypeOption $extraTypeOption*/
            $data[] = $extraTypeOption->getModifyComponent($key);
        }
        return Section::make($this->getName())
            ->icon($this->getIcon())
            ->collapsible()
            ->collapsed()
            ->columnSpanFull()
            ->columns()
            ->schema($data);
    }

}
