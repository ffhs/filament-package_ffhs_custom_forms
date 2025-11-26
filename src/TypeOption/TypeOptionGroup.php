<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption;


use Filament\Schemas\Components\Section;
use Filament\Support\Components\Component;


class TypeOptionGroup
{
    /** @var array<string, TypeOption> $typeOptions */
    protected array $typeOptions;
    protected string $name;
    protected ?string $icon;

    /**
     * @param string $name
     * @param array<int|string, TypeOption> $typeOptions
     * @param string|null $icon
     */
    public function __construct(string $name, array $typeOptions, ?string $icon = null)
    {
        $this->name = $name;
        $this->typeOptions = $typeOptions;
        $this->icon = $icon;
    }

    /**
     * @param string $name
     * @param array<int|string, TypeOption> $typeOptions
     * @param string|null $icon
     *
     * @return static
     */
    public static function make(string $name, array $typeOptions, ?string $icon = null): static
    {
        return app(static::class, ['name' => $name, 'typeOptions' => $typeOptions, 'icon' => $icon]);
    }


    /**
     * @param array<int|String, TypeOption> $typeOptions
     * @return $this
     */
    public function mergeTypeOptions(array $typeOptions): static
    {
        foreach ($typeOptions as $key => $typeOption) {
            $this->addTypeOptions($key, $typeOption);
        }

        return $this;
    }

    public function addTypeOptions(string $key, TypeOption $typeOption): static
    {
        $this->typeOptions[$key] = $typeOption;

        return $this;
    }

    public function getModifyOptionComponent(): Section
    {
        return once(function (): Section {
            /** @var Component[] $schema */
            $schema = [];

            foreach ($this->getTypeOptions() as $key => $extraTypeOption) {
                $schema[] = $extraTypeOption->getModifyOptionComponent($key);
            }

            return Section::make($this->getName())
                ->icon($this->getIcon())
                ->columnSpanFull()
                ->schema($schema)
                ->collapsible()
                ->collapsed()
                ->columns();
        });
    }

    /** @return array<string|int, TypeOption> */
    public function getTypeOptions(): array
    {
        return $this->typeOptions;
    }

    /**
     * @param array<string|int, TypeOption> $typeOptions
     * @return $this
     */
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
