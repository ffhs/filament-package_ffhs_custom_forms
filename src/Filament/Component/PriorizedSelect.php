<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;

class PriorizedSelect extends Field
{
    use HasOptions;
    use CanLimitItemsLength;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.prioritized-select';
    protected bool|Closure $dynamic = false;
    protected Closure $mutateSelectUsing;
    protected string|Closure $preKey = 'prioritized_';

    public function dynamic(Closure|bool $dynamic = true): static
    {
        $this->dynamic = $dynamic;
        return $this;
    }

    public function preKey(Closure|string $preKey = 'prioritized_'): static
    {
        $this->preKey = $preKey;
        return $this;
    }

    public function getChildComponents(): array
    {

        $maxSelect = $this->getMaxItems();
        $minSelect = $this->getMinItems();
        $preKey = $this->getPreKey();

        $isSelectRequired = function ($selectId) use ($minSelect) {
            return $this->isRequired() && $minSelect!= 0 && $minSelect > $selectId;
        };

        $selects = [];
        for ($selectId = 0; $selectId < $maxSelect; $selectId++) {

            $select = Select::make($preKey . $selectId)
                ->required(fn() => $isSelectRequired($selectId))
                ->options(fn() => $this->getOptions())
                ->disableOptionWhen(function ($get, $value) use ($maxSelect, $preKey, $selectId) : bool{
                    for ($i = 0; $i < $maxSelect; $i++) {
                        if($i == $selectId) continue;
                        if($get($preKey . $i) == $value) return true;
                    }
                    return false;
                });


            if($minSelect-1 <= $selectId ) {
                $select->afterStateUpdated(function ($set, $state) use ($minSelect, $preKey, $maxSelect, $selectId) {
                    if(!$this->isDynamic()) return;
                    if($state != null) return;
                    for ($i = $selectId; $i < $maxSelect; $i++) {
                        if($minSelect <= $i) $set($preKey . $i, null);
                    }
                });
            }

            if($minSelect <= $selectId && $selectId > 0){
                $select->visible(function ($get) use ($selectId, $preKey) {
                    if(!$this->isDynamic()) return true;
                    return $get($preKey . $selectId-1);
                });
            }

            $selects[] = $this->mutateSelect($select, $selectId);
        }

        return $selects;
    }

    public function getPreKey(): string
    {
        return $this->evaluate($this->preKey);
    }

    public function isDynamic(): bool
    {
        return $this->evaluate($this->dynamic);
    }

    public function mutateSelect(Select $select, int $selectId): Select
    {
        return $this->evaluate($this->mutateSelectUsing, ["selectId" => $selectId, "select" => $select]);
    }

    protected function setUp(): void
    {
         parent::setUp();
         $this->mutateSelectUsing(fn(Select $select) => $select);
    }

    public function mutateSelectUsing(Closure $mutateSelectUsing): static
    {
        $this->mutateSelectUsing = $mutateSelectUsing;
        return $this;
    }



}
