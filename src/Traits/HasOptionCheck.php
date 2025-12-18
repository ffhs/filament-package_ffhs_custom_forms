<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;

trait HasOptionCheck
{
    use CanLoadFieldRelationFromForm;

    protected function checkOption(mixed $targetValue, array $data): bool
    {

        if (empty($data)) {
            return false;
        }

        $includeNull = $data['selected_include_null'] ?? false;
        $options = $data['selected_options'] ?? [];

        if ($includeNull && empty($targetValue)) {
            return true;
        }

        if (empty($options)) {
            return false;
        }

        //Custom Option Types like Select
        if (is_array($targetValue)) {
            return count(array_intersect($targetValue, $options)) > 0;
        }

        return !is_null($targetValue) && in_array($targetValue, $options, false);
    }

    protected function getOptionTypeGroup(): Group
    {
        return Group::make([
            Checkbox::make('selected_include_null')
                ->label(static::__('options.selected_include_null')),
            Select::make('selected_options')
                ->label(static::__('options.selected_options'))
                ->options($this->getOptionTypeGroupOptions(...))
                ->columnSpanFull()
                ->multiple(),
        ]);
    }

    protected function getOptionTypeGroupOptions(Get $get, $record): array|Collection
    {
        $finalField = $this->getTargetFieldData($get);

        if (is_null($finalField)) {
            return [];
        }

        if (!array_key_exists('customOptions', $finalField->options ?? [])) {
            return [];
        }

        if ($finalField->isGeneralField()) {
            //GeneralFields
            $genField = $finalField->getGeneralField();
            $options = collect($finalField['options']['customOptions']);

            return $genField?->customOptions
                ->whereIn('id', $options)
                ->pluck('name', 'identifier')
                ->toArray() ?? [];
        }


        $options = collect($finalField->options['customOptions'] ?? []);
        $local = method_exists($record, 'getLocale') ? $record->getLocale() : app()->getLocale();

        return $options->pluck('name.' . $local, 'identifier');
    }
}
