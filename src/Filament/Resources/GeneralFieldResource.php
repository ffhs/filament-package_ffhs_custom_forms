<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages\{CreateGeneralField,
    EditGeneralField,
    ListGeneralField};
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\RelationManagers\GeneralFieldFormRelationManager;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Database\Eloquent\Builder;

class GeneralFieldResource extends Resource
{
    use Translatable;

    public const langPrefix = 'filament-package_ffhs_custom_forms::custom_forms.fields.';
    protected static ?string $model = GeneralField::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name_' . app()->getLocale();
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('filamentResource');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
    }

    public static function getTitleCaseModelLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.fields.general_field');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('generalFieldForms');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->label(__(self::langPrefix . 'name'))
                            ->columnStart(1)
                            ->columnSpan(1),
                        Group::make([
                            Select::make('type')
                                ->options(function (Select $component) {
                                    if (!$component->isDisabled()) {
                                        $types = CustomFieldType::getSelectableGeneralFieldTypes();
                                    } else {
                                        $types = CustomFieldType::getAllTypes();
                                    }

                                    $keys = array_keys($types);
                                    $values = array_map(
                                        fn(string $type) => CustomFieldType::getTypeFromIdentifier($type)
                                            ->getTranslatedName(),
                                        $keys
                                    );
                                    return array_combine($keys, $values);
                                })
                                ->label(__(self::langPrefix . 'type'))
                                ->helperText(__(self::langPrefix . 'helper_text.type'))
                                ->disabledOn('edit')
                                ->columnStart(1)
                                ->columnSpan(1)
                                ->required()
                                ->live(),
                            IconPicker::make('icon')
                                ->required()
                                ->label('Icon'),
                        ]),
                        TextInput::make('identifier')
                            ->label(__(self::langPrefix . 'identifier'))
                            ->helperText(__(self::langPrefix . 'helper_text.identifier'))
                            ->disabled(fn($livewire) => $livewire instanceof EditRecord)
                            ->columnSpan(1)
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__(self::langPrefix . 'is_general_field_active'))
                            ->helperText(__(self::langPrefix . 'helper_text.is_general_field_active'))
                            ->default(true)
                            ->columnSpan(1),
                    ]),
                Fieldset::make('Einstellungen zum Überschreiben') //ToDo Translate
                ->columnSpan(1)
                    ->columns(1)
                    ->hidden(function ($get, $record) {
                        if (is_null($get('type'))) {
                            return false;
                        }

                        $type = CustomFieldType::getTypeFromIdentifier($get('type'));
                        $array = $type->getFlattenExtraTypeOptions();

                        return empty($array);
                    })
                    ->schema([
                        Select::make('extra_option_names')
                            ->label('')
                            ->multiple()
                            ->live()
                            ->formatStateUsing(fn(GeneralField $record) => array_keys($record->overwrite_options ?? []))
                            ->options(function ($get) {
                                $type = CustomFieldType::getTypeFromIdentifier($get('type'));

                                if (is_null($get('type'))) {
                                    return [];
                                }

                                $key = array_keys($type->getFlattenExtraTypeOptions());

                                return array_combine($key, $key);
                            }),
                        Group::make()
                            ->statePath('overwrite_options')
                            ->schema(function ($get) {
                                if (is_null($get('type'))) {
                                    return [];
                                }

                                $type = CustomFieldType::getTypeFromIdentifier($get('type'));
                                $components = $type->getExtraTypeOptionComponents();

                                $isOverwritten = fn($get, $component) => in_array(
                                    $component->getStatePath(false),
                                    $get('../extra_option_names') ?? []
                                );

                                foreach ($components as $item) {
                                    if ($item instanceof Field) {
                                        $item->visible($isOverwritten);
                                    } elseif ($item instanceof Section) {
                                        //Hide if all child hidden
                                        $item->visible(static function (Component $component): bool {
                                            return count($component->getChildComponentContainer()->getComponents());
                                        });

                                        foreach ($item->getChildComponents() as $field) {
                                            $field->visible($isOverwritten);
                                        }
                                    }
                                }

                                return $components;
                            })
                    ]),
                Fieldset::make('Optionen') //ToDo Translate
                //ToDo make by saving option and by load option (TypeOption)
                //ToDo fix  it  show the field is required (Error) if the field is filled
                ->columnSpan(1)
                    ->columns(1)
                    ->statePath('options')
                    ->visible(function ($get) {
                        if (is_null($get('type'))) {
                            return false;
                        }
                        $type = CustomFieldType::getTypeFromIdentifier($get('type'));
                        return count($type->generalTypeOptions()) > 0;
                    })
                    ->schema(function ($get) {
                        if (is_null($get('type'))) {
                            return [];
                        }

                        $type = CustomFieldType::getTypeFromIdentifier($get('type'));
                        return $type->getGeneralTypeOptionComponents();
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label('Icon')
                    ->icon(fn($state) => $state),
                TextColumn::make('name')
                    ->label(__(self::langPrefix . 'label')),
                TextColumn::make('type')
                    ->label(__(self::langPrefix . 'type'))
                    ->getStateUsing(function (GeneralField $record) {
                        return $record->getType()->getTranslatedName();
                    }),
                TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(__(self::langPrefix . 'form_connections'))
                    ->listWithLineBreaks()
                    ->state(fn(GeneralField $record) => $record->generalFieldForms
                        ->map(fn($generalFieldForm) => $generalFieldForm->dynamicFormConfiguration())
                        ->map(fn(DynamicFormConfiguration $class) => ($class)::displayName())
                    ),

                ToggleColumn::make('is_active')
                    ->label(__(self::langPrefix . 'is_general_field_active')),

            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GeneralFieldFormRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeneralField::route('/'),
            'create' => CreateGeneralField::route('/create'),
            'edit' => EditGeneralField::route('/{record}/edit'),
        ];
    }

    private static function getTranslationTab(string $location, string $label): Tab
    {
        return Tab::make($label)
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('tool_tip')
                    ->label(__(self::langPrefix . 'tool_tip')),
            ]);
    }
}
