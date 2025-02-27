<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\SelectTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SelectType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'select';
    }

    public function viewModes(): array
    {
        return [
            'default' => SelectTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'carbon-select-window';
    }


    public function extraTypeOptions(): array
    {
        return
            [
                LayoutOptionGroup::make(),
                ValidationTypeOptionGroup::make()
                    ->mergeTypeOptions([
                        'several' => new FastTypeOption(
                            false,
                            Toggle::make('several') //ToDo Put it in own option
                            ->label(TypeOption::__('several.label'))
                                ->helperText(TypeOption::__('several.helper_text'))
                                ->columnSpanFull()
                                ->live()
                        ),
                        'prioritized' => new FastTypeOption(
                            false,
                            Toggle::make('prioritized')
                                ->whenTruthy('several')
                                ->label(TypeOption::__('prioritized.label'))
                                ->helperText(TypeOption::__('prioritized.helper_text'))
                                ->live()
                        ),
                        'dynamic_prioritized' => new FastTypeOption(
                            false,
                            Toggle::make('dynamic_prioritized')
                                ->whenTruthy('prioritized')
                                ->label(TypeOption::__('dynamic_prioritized.label'))
                                ->helperText(TypeOption::__('dynamic_prioritized.helper_text'))
                        ),
                        'min_select' => new FastTypeOption(
                            1,
                            TextInput::make('min_select') //ToDo Replace With min_items
                            ->helperText(TypeOption::__('min_select.helper_text'))
                                ->label(TypeOption::__('min_select.label'))
                                ->required(fn($get) => $get('prioritized'))
                                ->whenTruthy('several')
                                ->columnStart(1)
                                ->minValue(0)
                                ->step(1)
                                ->required()
                                ->numeric(),
                        ),
                        'max_select' => new FastTypeOption(
                            1,
                            TextInput::make('max_select')
                                ->helperText(TypeOption::__('max_select.helper_text'))
                                ->label(TypeOption::__('max_select.label'))
                                ->required(fn($get) => $get('prioritized'))
                                ->whenTruthy('several')
                                ->minValue(0)
                                ->step(1)
                                ->required()
                                ->numeric(),
                        ),
                    ]),
                CustomOptionGroup::make(),
            ];
    }


}
