<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\TagsTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;

class TagsType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'tags_input';
    protected static string $icon = 'bi-tags';
    protected static array $viewModes = [
        'default' => TagsTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make(),
            ValidationTypeOptionGroup::make(),


        ];
    }
}
