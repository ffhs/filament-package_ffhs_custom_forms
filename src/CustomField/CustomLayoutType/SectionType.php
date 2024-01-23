<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids\HasCustomFormPackageTranslation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SectionType extends CustomLayoutType
{

    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "section";
    }

    public function viewModes(): array {
        return [
            "default" => SectionTypeView::class
        ];
    }

    public function getExtraOptionFields(): array {
        return [
            'column_span' => 3,
            'columns' => 4,
            'show_title' => true,
            'aside' => false,
            'new_line_option'=> true,
        ];
    }

    public function getExtraOptionSchema(): ?array {
        return [

            $this->getColumnSpanOption(),
            TextInput::make("columns")
                ->label("Anzahl Spalten") //ToDo Translate
                ->maxValue(10)
                ->minValue(1)
                ->step(1)
                ->required()
                ->integer(),
            Toggle::make("show_title")
                ->label("Titel Anzeigen") //ToDo Translate
                ->columnSpan(2)
                ->columnStart(1),
            Toggle::make("aside")
                ->label("Seitlich Anzeigen") //ToDo Translate
                ->columnStart(1),
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption(),
        ];
    }


    public function icon(): string {
       return  "tabler-section";
    }
}
