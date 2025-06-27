<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\TempCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
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
            return sizeof(array_intersect($targetValue, $options)) > 0;
        }

        return !is_null($targetValue) && in_array($targetValue, $options, false);
    }

    protected function getOptionTypeGroup(): Component
    {
        return Group::make([
            Checkbox::make('selected_include_null')
                ->label(static::__('options.selected_include_null')),
            Select::make('selected_options')
                ->label(static::__('options.selected_options'))
                ->options(fn($get, $record) => once(fn() => $this->getOptionTypeGroupOptions($get, $record)))
                ->columnSpanFull()
                ->multiple(),
        ]);
    }

    protected function getOptionTypeGroupOptions(Get $get, CustomForm $record): array|Collection
    {
        $finalField = $this->getTargetFieldData($get, $record);

        if (is_null($finalField)) {
            return [];
        }

        if (array_key_exists('general_field_id', $finalField) && !is_null($finalField['general_field_id'])) {
            //GeneralFields
            $tempField = new TempCustomField($record, $finalField);
            $genField = $tempField->generalField;

            if (!array_key_exists('options', $finalField)
                || !array_key_exists('customOptions', $finalField['options'])) {
                return [];
            }

            $options = collect($finalField['options']['customOptions']);

            return $genField->customOptions
                ->whereIn('id', $options)
                ->pluck('name', 'identifier')
                ->toArray();
        }

        if (!array_key_exists('options', $finalField)
            || !array_key_exists('customOptions', $finalField['options'])) {
            return [];
        }

        $options = collect($finalField['options']['customOptions']);

        return $options->pluck('name.' . $record->getLocale(), 'identifier');
    }
}
