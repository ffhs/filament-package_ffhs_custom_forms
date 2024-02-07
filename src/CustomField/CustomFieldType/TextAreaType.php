<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class TextAreaType extends CustomFieldType
{

    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "textarea";}


    public function viewModes(): array {
        return  [
          'default'=>   Views\TextAreaTypeView::class
        ];
    }

    protected function getExtraOptionFieldsBasicOptions(): array {
        return [
            'max_length' => 100,
            'min_length' => 0,
            'auto_size'=> true,
        ];
    }

    protected function getExtraOptionSchemaBasicOptions(): array {
        return [
            TextInput::make("max_length")
                ->label("Maximale Länge") //ToDo Translate
                ->columnStart(1)
                ->step(1)
                ->required()
                ->integer(),
            TextInput::make("min_length")
                ->label("Minimale Länge") //ToDo Translate
                ->step(1)
                ->required()
                ->integer(),
            Toggle::make("auto_size")
                ->label("Automatische Grösse") //ToDo Translate
                ->columnSpan(2),
        ];
    }


    public function icon(): string {
        return  "bi-textarea-t";
    }
}
