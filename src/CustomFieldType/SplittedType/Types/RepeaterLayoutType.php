<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\CustomSplitType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views\RepeaterLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ActionLabelTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

class RepeaterLayoutType extends CustomSplitType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'repeater';
    }

    public function viewModes(): array
    {
        return [
            'default' => RepeaterLayoutTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'heroicon-m-wallet';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make()
                ->mergeTypeOptions([
                    'show_as_fieldset' => ShowAsFieldsetOption::make(),
                    'add_action_label' => ActionLabelTypeOption::make()
                        ->modifyOptionComponent(fn(Field $component) => $component->columnSpan(1)),
                    'default_amount' => FastTypeOption::makeFast(
                        1,
                        TextInput::make('default_amount')
                            ->helperText(TypeOption::__('default_amount.helper_text'))
                            ->label(TypeOption::__('default_amount.label'))
                            ->lte('max_amount')
                            ->minValue(0)
                            ->integer()
                            ->required(),
                    ),
                ]),
            ValidationTypeOptionGroup::make()
                ->removeTypeOption('required')
                ->mergeTypeOptions([
                    'min_amount' => MinAmountOption::make(),
                    'max_amount' => MaxAmountOption::make(),
                ]),
        ];
    }
}
