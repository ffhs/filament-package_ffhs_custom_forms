<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\SelectTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
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
                            Toggle::make('several')
                                ->label(
                                    __('filament-package_ffhs_custom_forms::custom_forms.fields.type_options.several')
                                )
                                ->columnSpanFull()
                                ->live()
                        ),
                        'prioritized' => new FastTypeOption(
                            false,
                            Toggle::make('prioritized')
                                ->whenTruthy('several')
                                ->label(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.prioritized'
                                    )
                                )
                                ->helperText(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.prioritized_helper'
                                    )
                                )
                                ->live()
                        ),
                        'dynamic_prioritized' => new FastTypeOption(
                            false,
                            Toggle::make('dynamic_prioritized')
                                ->whenTruthy('prioritized')
                                ->label(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.dynamic_prioritized'
                                    )
                                )
                                ->helperText(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.dynamic_prioritized_helper'
                                    )
                                )
                        ),
                        'min_select' => new FastTypeOption(
                            1,
                            TextInput::make('min_select')
                                ->required(fn($get) => $get('prioritized'))
                                ->whenTruthy('several')
                                ->label(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select'
                                    )
                                )
                                ->columnStart(1)
                                ->helperText(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.min_select_helper'
                                    )
                                )
                                ->minValue(0)
                                ->step(1)
                                ->required()
                                ->numeric(),
                        ),
                        'max_select' => new FastTypeOption(
                            1,
                            TextInput::make('max_select')
                                ->required(fn($get) => $get('prioritized'))
                                ->whenTruthy('several')
                                ->label(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select'
                                    )
                                )
                                ->helperText(
                                    __(
                                        'filament-package_ffhs_custom_forms::custom_forms.fields.type_options.max_select_helper'
                                    )
                                )
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
