<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
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


    protected function extraOptionsBeforeBasic(): array {
        return [
            "columns" => new ColumnsOption(),
            "show_title" =>  new FastTypeOption(true,
                    Toggle::make("show_title")
                        ->label("Titel Anzeigen") //ToDo Translate
                        ->columnSpan(2)
                        ->columnStart(1)
                        ->live(),
                ),
            'aside' => new FastTypeOption(false,
                    Toggle::make("aside")
                        ->label("Titel seitlich Anzeigen") //ToDo Translate,
                        ->disabled(fn($get) => !$get("show_title"))
                )
        ];
    }

}
