<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\TextTypeView;
use Filament\Forms\Components\TextInput;

class TextType extends CustomFieldType
{

    public static function getFieldIdentifier(): string {return "text";}



    public function getExtraOptionFields(): array {
        return [
          'max_size' => 100
        ];
    }

    public function getExtraOptionSchema(): ?array {
        return [
          TextInput::make("max_size")
              ->step(1)
            ->integer()
        ];
    }


    public function viewModes(): array {
        return [
          'default' => TextTypeView::class
        ];
    }
}
