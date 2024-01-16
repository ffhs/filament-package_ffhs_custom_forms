<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;

class TextType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "text";}


    public function getExtraOptionFields(): array {
        return [
          'max_length' => 100,
          'min_length' => 0,
          'column_span' => 3,
          'in_line_label'=> false,
          'new_line_option'=> true,
        ];
    }

    public function getExtraOptionSchema(): ?array {
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
           $this->getColumnSpanOption(),
           $this->getNewLineOption()->columnStart(1),
           $this->getInLineLabelOption(),
        ];
    }


    public function viewModes(): array {
        return [
          'default' => TextTypeView::class
        ];
    }
}
