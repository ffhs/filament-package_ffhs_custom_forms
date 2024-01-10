<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\Pages\{CreateGeneralField,ListGeneralField,EditGeneralField};
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\RelationManagers\GeneralFieldFormRelationManager;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneralFieldResource extends Resource
{
    protected static ?string $model = GeneralField::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    const langPrefix= "filament-package_ffhs_custom_forms::custom_forms.fields.";

    public static function getRecordTitleAttribute(): ?string {
        return "name_" . app()->getLocale();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
    }

    public static function getNavigationParentItem(): ?string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }

    public static function getTitleCasePluralModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
    }

    public static function getTitleCaseModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.fields.general_field');
    }


    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->with("generalFieldForms");
    }


    private static function getTranslationTab(string $location, string $label): Tab {
        return Tab::make($label)
            ->schema([
                TextInput::make("name_" . $location)
                    ->label("Name")
                    ->required(),
                TextInput::make("tool_tip_" . $location)
                    ->label(__(self::langPrefix . 'tool_tip')),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([

                        Tabs::make()
                            ->tabs([
                                self::getTranslationTab("de","Deutsch"),
                                self::getTranslationTab("en","Englisch"),
                            ]),

                        Select::make("type")
                            ->options(function (Select $component){
                                //Skip selectable
                                $types = CustomFieldType::getAllTypes();
                                $keys = array_keys($types);
                                if(!$component->isDisabled()) {
                                    $disabled = config("ffhs_custom_forms.disabled_general_field_types");
                                    $keys = array_filter($keys,fn($type) => ! in_array(CustomFieldType::getTypeClassFromName($type),$disabled));
                                }
                                $values = array_map(fn(string $type) => CustomFieldType::getTypeFromName($type)->getTranslatedName(), $keys);
                                return array_combine($keys,$values);
                            })
                            ->label(__(self::langPrefix . 'type'))
                            ->helperText(__(self::langPrefix . 'helper_text.type'))
                            ->disabledOn("edit")
                            ->columnStart(1)
                            ->columnSpan(1)
                            ->required()
                            ->live(),

                        TextInput::make("identify_key")
                            ->label(__(self::langPrefix . 'identify_key'))
                            ->helperText(__(self::langPrefix . 'helper_text.identify_key'))
                            ->columnStart(1)
                            ->columnSpan(1)
                            ->required(),

                       Toggle::make("is_general_field_active")
                            ->label(__(self::langPrefix . 'is_general_field_active'))
                            ->helperText(__(self::langPrefix . 'helper_text.is_general_field_active'))
                            ->default(true)
                            ->columnStart(2)
                            ->columnSpan(1),


                        //Extra field FromType
                       Group::make(function ($get){
                            if(is_null($get("type"))) return[];
                            $type = CustomFieldType::getTypeFromName($get("type"));
                            if(is_null($type)) return [];
                            $component = $type->getGeneralFieldExtraField();
                            return is_null($component)?[]:[$component];
                        })->columnSpanFull(),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__(self::langPrefix . 'label'))
                    ->getStateUsing(function ($record){
                        return $record->toArray()["name_" .app()->getLocale()];
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label(__(self::langPrefix .'type'))
                    ->getStateUsing(function ($record){
                        return __(self::langPrefix .'types.'.$record->type);
                    }),

                Tables\Columns\TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(__(self::langPrefix . 'form_connections'))
                    ->listWithLineBreaks()
                    ->state(fn(GeneralField $record) =>
                        $record->generalFieldForms
                            ->map(fn($generalFieldForm) => $generalFieldForm->dynamicFormConfiguration())
                            ->map(fn(string $class) => ($class)::displayName())
                    ),

                Tables\Columns\ToggleColumn::make('is_general_field_active')
                    ->label(__(self::langPrefix .'is_general_field_active')),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
