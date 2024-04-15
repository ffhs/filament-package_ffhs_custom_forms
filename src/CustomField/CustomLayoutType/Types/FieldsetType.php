<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\FieldsetTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\SectionTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\Toggle;

class FieldsetType extends CustomLayoutType
{

    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "fieldset";
    }

    public function viewModes(): array {
        return [
            "default" => FieldsetTypeView::class
        ];
    }

    public function icon(): string {
        return  "heroicon-m-rectangle-group";
    }

    public function getExtraTypeOptions(): array {
        return[
            "columns" => new ColumnsOption(),
            'column_span' => new ColumnSpanOption(),
            "show_title" =>  new ShowTitleOption(),
            'show_in_view'=> new ShowInViewOption(),
            'new_line_option' => new NewLineOption(),
        ];
    }

}
