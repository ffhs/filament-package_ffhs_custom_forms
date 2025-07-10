<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;

trait HasBoolCheck
{
    protected function checkBoolean(mixed $targetValue, array $data): bool
    {
        if (is_null($targetValue)) {
            return false;
        }

        if (empty($data['boolean'])) {
            $boolean = false;
        } else {
            $boolean = $data['boolean'];
        }

        if (!is_bool($boolean)) {
            $boolean = (bool)$boolean;
        }

        if (!is_bool($targetValue)) {
            $targetValue = (bool)$targetValue;
        }

        return $targetValue === $boolean;
    }

    protected function getBooleanTypeGroup(): Component
    {
        return Group::make([
            Checkbox::make('boolean')
                ->label(static::__('bool.trigger_on_true'))
                ->columnSpanFull(),
        ]);
    }
}
