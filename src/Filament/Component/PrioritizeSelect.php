<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Closure;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;

class PrioritizeSelect extends Field
{
    use HasOptions;
    use CanLimitItemsLength{
        CanLimitItemsLength::getMinItems as protected parentGetMinItems;
        CanLimitItemsLength::getMaxItems as parentGetMaxItems;
    }

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.prioritized-select';
    protected bool|Closure $dynamic = false;
    protected Closure $mutateSelectUsing;
    protected string|Closure $preKey = 'prioritized_';

    public function minItems(int|Closure|null $count): static
    {
        $this->required();
        $this->minItems = $count;
        return $this;
    }

    public function maxItems(int|Closure|null $count): static
    {
        $this->maxItems = $count;
        return $this;
    }


    public function getMinItems(): ?int
    {
        $optionAmounts = sizeof($this->getOptions());
        $minItems =  $this->parentGetMinItems() ?? 0;
        return $minItems > $optionAmounts? $optionAmounts: $minItems;
    }

    public function getMaxItems(): ?int
    {
        $optionAmounts = sizeof($this->getOptions());
        $minItems =  $this->parentGetMaxItems() ?? 0;
        return $minItems > $optionAmounts? $optionAmounts: $minItems;
    }




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

        $selects = [];
        for ($selectId = 0; $selectId < $maxSelect; $selectId++) {

            $select = $this->getSingleSelect($selectId);

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


    protected function configureSingleSelectDisableOptionWhen(Select $select, int $selectId): Select
    {
        return $select->disableOptionWhen(function ($get, $value) use ( $selectId) : bool{
                for ($i = 0; $i < $this->getMaxItems(); $i++) {
                    if($i == $selectId) continue;
                    if($get($this->getPreKey() . $i) == $value) return true;
                }
                return false;
            });
    }

    protected function configureDynamicSelectReset(Select $select, int $selectId): Select
    {
        return $select->afterStateUpdated(function ($set, $state) use ($selectId) {
                if($this->getMinItems()-1 > $selectId) return;
                if(!$this->isDynamic() || $state != null) return;
                for ($i = $selectId; $i < $this->getMaxItems(); $i++) {
                    if($this->getMinItems() <= $i) $set($this->getPreKey() . $i, null);
                }
            });
    }

    protected function configureDynamicSelectVisibility(Select $select, int $selectId): Select
    {
        return $select->visible(function ($get) use ($selectId) {
            if($this->getMinItems() > $selectId || $selectId == 0 || !$this->isDynamic())
                return true;
            return $get($this->getPreKey() . $selectId-1);
        });
    }

    protected function configureSingleSelectRequired(Select $select, int $selectId): Select
    {
        return $select->required(function () use ($selectId) {
            if(!$this->isRequired()) return true;
            if($this->getMinItems()== 0) return true;
            return $this->getMinItems() > $selectId;
        });
    }



    protected function getSingleSelect(int $selectId): Select
    {
        $id = $this->getPreKey() . $selectId;
        $select = Select::make($id)->options($this->options);

        $select = $this->configureSingleSelectRequired($select, $selectId);
        $select = $this->configureSingleSelectDisableOptionWhen($select, $selectId);

        $select = $this->configureDynamicSelectReset($select, $selectId);

        return $this->configureDynamicSelectVisibility($select, $selectId);
    }


}
