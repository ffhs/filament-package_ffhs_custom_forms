<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\NumberTypeView;
use Filament\Forms\Components\TextInput;

class NumberType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function getFieldIdentifier(): string {return "number";}



    public function getExtraOptionSchema(): ?array {
        return [
            TextInput::make("step") // ToDo: Translate
                ->numeric()
                ->placeholder(1)
                ->step(1)
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'step'=>null,
        ];
    }

    public function viewModes(): array {
        return [
          'default' => NumberTypeView::class
        ];
    }
}
