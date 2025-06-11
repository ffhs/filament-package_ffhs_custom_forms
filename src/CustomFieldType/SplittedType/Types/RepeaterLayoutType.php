<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\CustomSplitType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views\RepeaterLayoutTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutWithColumnsOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ActionLabelTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\MaxAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\MinAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class RepeaterLayoutType extends CustomSplitType
{

    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "repeater";
    }

    public function viewModes(): array
    {
        return [
            "default" => RepeaterLayoutTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "heroicon-m-wallet";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutWithColumnsOptionGroup::make()
                ->mergeTypeOptions([
                    'show_label' => ShowLabelOption::make(),
                    'show_as_fieldset' => ShowAsFieldsetOption::make()
                        ->modifyOptionComponent(fn(Component $component) => $component->columnStart(2)),
                    'default_amount' => FastTypeOption::makeFast(
                        1,
                        TextInput::make("default_amount")
                            ->helperText(TypeOption::__('default_amount.helper_text'))
                            ->label(TypeOption::__('default_amount.label'))
                            ->lte("max_amount")
                            ->minValue(0)
                            ->integer()
                            ->required(),
                    ),
                    'add_action_label' => ActionLabelTypeOption::make(),
//                    'ordered' => new FastTypeOption(false,
//                        Toggle::make('ordered')
//                            ->default(false)
//                            ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.type_options.ordered'))
//                    ),
                ]),
            ValidationTypeOptionGroup::make()
                ->removeTypeOption("required")
                ->mergeTypeOptions([
                    "min_amount" => MinAmountOption::make(),
                    "max_amount" => MaxAmountOption::make(),
                ]),
        ];
    }

}
