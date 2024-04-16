<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
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

    public function icon(): string {
        return  "tabler-section";
    }

    public function getExtraTypeOptions(): array {
        return[
            "columns" => new ColumnsOption(),
            'column_span' => new ColumnSpanOption(),
            "show_title" =>  new ShowTitleOption(),
            'aside' => new FastTypeOption(false,
                Toggle::make("aside")
                    ->label("Titel seitlich Anzeigen") //ToDo Translate,
                    ->disabled(fn($get) => !$get("show_title"))
            ),
            //'in_line_label' => (new InLineLabelOption())->modifyComponent(fn($toggle) => $toggle->columnStart(1)),
            'new_line_option' => new NewLineOption(),

            'show_as_fieldset' => new ShowAsFieldsetOption(),
            'show_in_view'=> new ShowInViewOption(),
        ];
    }

}
