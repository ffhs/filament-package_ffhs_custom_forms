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

    public function getExtraOptionFields(bool $isInheritGeneral = false): array {
        return [
            'max_length' => 100,
            'min_length' => 0,
            'column_span' => 3,
            'in_line_label'=> false,
            'new_line_option'=> true,
            'auto_size'=> true,
        ];
    }

    public function getExtraOptionSchema(bool $isInheritGeneral = false): ?array {
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
            $this->getColumnSpanOption(),
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption()
        ];
    }


    public function icon(): string {
        return  "bi-textarea-t";
    }
}
