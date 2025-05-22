<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TagsTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;

class TagsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "tags_input";
    }

    public function viewModes(): array
    {
        return [
            'default' => TagsTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-tags";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make(),
            ValidationTypeOptionGroup::make(),


        ];
    }


}
