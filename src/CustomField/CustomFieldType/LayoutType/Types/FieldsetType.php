<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\FieldsetTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;

class FieldsetType extends CustomLayoutType
{

    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {
        return "fieldset";
    }

    public function viewModes(): array {
        return [
            "default" => FieldsetTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-columns-gap";
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
