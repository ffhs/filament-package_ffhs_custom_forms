<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\GroupTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;

class GroupType extends CustomLayoutType
{
    use HasBasicSettings;
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {
        return "group";
    }

    public function viewModes(): array {
        return [
            "default" => GroupTypeView::class
        ];
    }

    public function icon(): string {
        return  "carbon-cics-system-group";
    }

    public function extraTypeOptions(): array {
        return[
            "columns" => new ColumnsOption(),
            'column_span' => new ColumnSpanOption(),
            'new_line_option' => new NewLineOption(),
            'show_in_view'=> new ShowInViewOption(),
        ];
    }

}
