<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Group;

trait HasTextCheck
{
    /**
     * @param mixed $targetValue
     * @param array<string, array<string|int, string>> $data
     * @return bool
     */
    protected function checkText(mixed $targetValue, array $data): bool
    {
        if (!is_string($targetValue) || empty($data['values'])) {
            return false;
        }

        foreach ($data['values'] as $value) {
            if (fnmatch($value, $targetValue)) {
                return true;
            }
        }

        return false;
    }

    protected function getTextTypeGroup(): Group
    {
        return Group::make([
            TagsInput::make('values')
                ->reorderable(false)
                ->columnSpanFull()
                ->hiddenLabel(),
        ]);
    }
}
