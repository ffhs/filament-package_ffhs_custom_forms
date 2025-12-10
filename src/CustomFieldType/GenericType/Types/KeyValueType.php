<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\KeyValueTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ReorderableTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Toggle;

class KeyValueType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'heroicon-o-key';
    protected static string $icon = 'carbon-character-whole-number';
    protected static array $viewModes = [
        'default' => KeyValueTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'reorderable' => ReorderableTypeOption::make(),
                    'editableKeys' => FastTypeOption::makeFast(
                        true,
                        static fn() => Toggle::make('editableKeys')
                            ->label(TypeOption::__('editable_keys.label'))
                            ->helperText(TypeOption::__('editable_keys.helper_text'))
                            ->columnSpanFull()
                    ),
                    'editableValues' => FastTypeOption::makeFast(
                        true,
                        static fn() => Toggle::make('editableValues')
                            ->label(TypeOption::__('editable_values.label'))
                            ->helperText(TypeOption::__('editable_keys.helper_text'))
                            ->columnSpanFull()
                    ),
                ]),
        ];
    }
}
