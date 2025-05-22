<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views\KeyValueTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ReorderableTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;

class KeyValueType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'key_value';
    }

    public function viewModes(): array
    {
        return [
            'default' => KeyValueTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'heroicon-o-key';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'reorderable' => ReorderableTypeOption::make(),
                    'editableKeys' => new FastTypeOption( //ToDo continue Translations
                        true,
                        Toggle::make('editableKeys')
                            ->label(TypeOption::__('editable_keys.label'))
                            ->helperText(TypeOption::__('editable_keys.helper_text'))
                            ->columnSpanFull()
                    ),
                    'editableValues' => new FastTypeOption(
                        true,
                        Toggle::make('editableValues')
                            ->label(TypeOption::__('editable_values.label'))
                            ->helperText(TypeOption::__('editable_keys.helper_text'))
                            ->columnSpanFull()
                    ),
                ]),
        ];
    }
}
