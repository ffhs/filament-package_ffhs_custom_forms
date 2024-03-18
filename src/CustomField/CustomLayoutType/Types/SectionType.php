<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SectionType extends CustomLayoutType
{

    use HasBasicSettings;
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {
        return "section";
    }

    public function viewModes(): array {
        return [
            "default" => SectionTypeView::class
        ];
    }

    public function icon(): string {
        return  "tabler-section";
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


    protected function extraOptionsBeforeBasic(): array {
        return [
            new FastTypeOption(4,
                TextInput::make("columns")
                    ->label("Anzahl Spalten") //ToDo Translate
                    ->maxValue(10)
                    ->minValue(1)
                    ->step(1)
                    ->required()
                    ->integer()
            ),
            new FastTypeOption(true,
                Toggle::make("show_title")
                    ->label("Titel Anzeigen") //ToDo Translate
                    ->columnSpan(2)
                    ->columnStart(1),
            ),
            new FastTypeOption(false,
                Toggle::make("aside")
                    ->label("Seitlich Anzeigen") //ToDo Translate
                    ->columnStart(1),
            )
        ];
    }

}
