<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField;



use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\App;
use function PHPUnit\Framework\isEmpty;

abstract class CustomLayoutType extends CustomFieldType
{

    public function getExtraOptionSchema():?array{
        return [
            TextInput::make("end_location")
                ->numeric()
                //->disabled()
        ];
    }

    public function getExtraOptionFields():array{
        return [
            "end_location" => null
        ];
    }

}
