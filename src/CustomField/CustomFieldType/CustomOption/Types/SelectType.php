<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\TypeOptions\CustomOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\Views\SelectTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ValidationMessageOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

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
                LayoutOptionGroup::make()
                    ->addTypeOptions(
                        'prioritized_labels',
                        FastTypeOption::makeFast(
                            [],
                            Repeater::make('prioritized_labels')
                                ->label(TypeOption::__('prioritized_labels.label'))
                                ->schema([
                                    TextInput::make('label')
                                        ->label(''),
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
                        'several' => FastTypeOption::makeFast(
                            false,
                            Toggle::make('several') //ToDo Put it in own option
                            ->label(TypeOption::__('several.label'))
                                ->helperText(TypeOption::__('several.helper_text'))
                                ->columnSpanFull()
                                ->live()
                        ),
                        'prioritized' => FastTypeOption::makeFast(
                            false,
                            Toggle::make('prioritized')
                                ->whenTruthy('several')
                                ->label(TypeOption::__('prioritized.label'))
                                ->helperText(TypeOption::__('prioritized.helper_text'))
                                ->live()
                        ),
                        'dynamic_prioritized' => FastTypeOption::makeFast(
                            false,
                            Toggle::make('dynamic_prioritized')
                                ->whenTruthy('prioritized')
                                ->label(TypeOption::__('dynamic_prioritized.label'))
                                ->helperText(TypeOption::__('dynamic_prioritized.helper_text'))
                        ),
                        'min_select' => FastTypeOption::makeFast(
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
                        'max_select' => FastTypeOption::makeFast(
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
                        'validation_messages' => ValidationMessageOption::make()
                            ->modifyOptionComponent(
                                fn(Component $component) => $component->hidden(fn(Get $get) => $get('prioritized'))
                            ),

                        'validation_messages_prioritized' => FastTypeOption::makeFast([],
                            Repeater::make('validation_messages_prioritized')
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
