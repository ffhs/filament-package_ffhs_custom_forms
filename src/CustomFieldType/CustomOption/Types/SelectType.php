<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views\SelectTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxSelectOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinSelectOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\SeveralOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ValidationMessageOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;

class SelectType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'select';
    protected static string $icon = 'carbon-select-window';
    protected static array $viewModes = [
        'default' => SelectTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->addTypeOptions(
                    'prioritized_labels',
                    FastTypeOption::makeFast(
                        [],
                        static fn($name) => Repeater::make($name)
                            ->label(TypeOption::__('prioritized_labels.label'))
                            ->schema([
                                TextInput::make('label')
                                    ->hiddenLabel(),
                            ])
                            ->whenTruthy('prioritized')
                            ->addActionLabel('prioritized_labels.add_label')
                            ->columnSpanFull()
                            ->reorderable()
                    )
                ),
            ValidationTypeOptionGroup::make()
                ->removeTypeOption('validation_messages')
                ->mergeTypeOptions([
                    'several' => SeveralOption::make(),
                    'prioritized' => FastTypeOption::makeFast(
                        false,
                        static fn($name) => Toggle::make($name)
                            ->whenTruthy('several')
                            ->label(TypeOption::__('prioritized.label'))
                            ->helperText(TypeOption::__('prioritized.helper_text'))
                            ->live()
                    ),
                    'dynamic_prioritized' => FastTypeOption::makeFast(
                        false,
                        static fn($name) => Toggle::make($name)
                            ->whenTruthy('prioritized')
                            ->label(TypeOption::__('dynamic_prioritized.label'))
                            ->helperText(TypeOption::__('dynamic_prioritized.helper_text'))
                    ),
                    'min_select' => MinSelectOption::make()
                        ->modifyOptionComponent(fn(Field $component) => $component
                            ->required(fn(Get $get) => $get('prioritized'))
                            ->whenTruthy('several')
                            ->columnStart(1)
                        ),
                    'max_select' => MaxSelectOption::make()
                        ->modifyOptionComponent(fn(Field $component) => $component
                            ->required(fn(Get $get) => $get('prioritized'))
                            ->whenTruthy('several')
                        ),
                    'validation_messages' => ValidationMessageOption::make()
                        ->modifyOptionComponent(
                            fn(Field $component) => $component->hidden(fn(Get $get) => $get('prioritized'))
                        ),
                    'validation_messages_prioritized' => FastTypeOption::makeFast([],
                        static fn($name) => Repeater::make($name)
                            ->visible(fn(Get $get) => $get('prioritized'))
                            ->label(TypeOption::__('validation_messages_prioritized.label'))
                            ->helperText(TypeOption::__('validation_messages_prioritized.helper_text'))
                            ->schema([
                                TextInput::make('select_id')
                                    ->label(TypeOption::__('validation_messages_prioritized.select_id.label'))
                                    ->helperText(
                                        TypeOption::__('validation_messages_prioritized.select_id.helper_text')
                                    )
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->integer()
                                    ->step(1),
                                TextInput::make('rule')
                                    ->label(TypeOption::__('validation_messages_prioritized.rule.label'))
                                    ->helperText(TypeOption::__('validation_messages_prioritized.rule.helper_text'))
                                    ->required(),
                                TextInput::make('message')
                                    ->label(TypeOption::__('validation_messages_prioritized.message.label'))
                                    ->helperText(
                                        TypeOption::__('validation_messages_prioritized.message.helper_text')
                                    )
                                    ->nullable()
                                    ->columnSpan(2),
                            ])
                            ->addActionLabel(TypeOption::__('validation_messages_prioritized.add_label'))
                            ->collapsible(false)
                            ->columns(3)
                            ->columnSpanFull()),
                ]),
            CustomOptionGroup::make(),
        ];
    }
}
