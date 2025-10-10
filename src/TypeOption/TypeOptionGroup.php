<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption;


use Filament\Schemas\Components\Section;


class TypeOptionGroup
{
    protected array $typeOptions;
    protected string $name;
    protected ?string $icon;

    public function __construct(string $name, array $typeOptions, ?string $icon = null)
    {
        $this->name = $name;
        $this->typeOptions = $typeOptions;
        $this->icon = $icon;
    }

    public static function make(string $name, array $typeOptions, ?string $icon = null): static
    {
        return app(static::class, ['name' => $name, 'typeOptions' => $typeOptions, 'icon' => $icon]);
    }

    public function mergeTypeOptions(array $typeOptions): static
    {
        foreach ($typeOptions as $key => $typeOption) {
            $this->addTypeOptions($key, $typeOption);
        }

        return $this;
    }

    public function addTypeOptions($key, TypeOption $typeOption): static
    {
        $this->typeOptions[$key] = $typeOption;

        return $this;
    }

    public function getModifyOptionComponent(): Section
    {
        return once(function () {
            $data = [];

            foreach ($this->getTypeOptions() as $key => $extraTypeOption) {
                /**@var TypeOption $extraTypeOption */
                $data[] = $extraTypeOption->getModifyOptionComponent($key);
            }

            return Section::make($this->getName())
                ->icon($this->getIcon())
                ->collapsible()
                ->collapsed()
                ->columnSpanFull()
                ->columns()
                ->schema($data);
        });
    }

    public function getTypeOptions(): ?array
    {
        return $this->typeOptions;
    }

    public function setTypeOptions(array $typeOptions): static
    {
        $this->typeOptions = $typeOptions;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TypeOptionGroup
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): TypeOptionGroup
    {
        $this->icon = $icon;

        return $this;
    }

    public function removeTypeOption(string $key): static
    {
        unset($this->typeOptions[$key]);

        return $this;
    }
}
