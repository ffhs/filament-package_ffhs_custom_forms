<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\Pages\{CreateGeneralField,
    EditGeneralField,
    ListGeneralField};
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
use Guava\FilamentIconPicker\Forms\IconPicker;
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
                TextInput::make("name")
                    ->label("Name")
                    ->required(),
                TextInput::make("tool_tip")
                    ->label(__(self::langPrefix . 'tool_tip')),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpan(2)
                    ->columns()
                    ->schema([

                        Tabs::make()
                            ->tabs([
                                self::getTranslationTab("de","Deutsch"),
                                self::getTranslationTab("en","Englisch"),
                            ]),

                       Group::make([
                           Select::make("type")
                               ->options(function (Select $component){
                                   if(!$component->isDisabled()) $types = CustomFieldType::getSelectableGeneralFieldTypes();
                                   else $types = CustomFieldType::getAllTypes();
                                   $keys = array_keys($types);
                                   $values = array_map(fn(string $type) => CustomFieldType::getTypeFromIdentifier($type)->getTranslatedName(), $keys);
                                   return array_combine($keys,$values);
                               })
                               ->label(__(self::langPrefix . 'type'))
                               ->helperText(__(self::langPrefix . 'helper_text.type'))
                               ->disabledOn("edit")
                               ->columnStart(1)
                               ->columnSpan(1)
                               ->required()
                               ->live(),
                           IconPicker::make('icon')
                               ->required()
                               ->label("Icon"),
                       ]),


                        TextInput::make("identifier")
                            ->label(__(self::langPrefix . 'identifier'))
                            ->helperText(__(self::langPrefix . 'helper_text.identifier'))
                            ->columnStart(1)
                            ->columnSpan(1)
                            ->required(),
                        Toggle::make("is_active")
                            ->label(__(self::langPrefix . 'is_general_field_active'))
                            ->helperText(__(self::langPrefix . 'helper_text.is_general_field_active'))
                            ->default(true)
                            ->columnSpan(1),


                    ]),


                Section::make("Optionen") //ToDo Translate
                    //ToDo make by saving option and by load option (TypeOption)
                    //ToDo fix  it  show the field is required (Error) if the field is filled
                    ->columnSpan(1)
                    ->columns(1)
                    ->collapsed()
                    ->statePath("options")
                    ->visible(function($get){
                        if(is_null($get("type"))) return false;
                        $type = CustomFieldType::getTypeFromIdentifier($get("type"));
                        return count($type->generalTypeOptions()) > 0;
                    })
                    ->schema(function($get){
                        if(is_null($get("type"))) return[];
                        $type = CustomFieldType::getTypeFromIdentifier($get("type"));
                        return $type->getGeneralTypeOptionComponents();
                    }),

                Section::make("Überschreiben Einstellungen") //ToDo Translate
                    ->columnSpan(1)
                    ->columns(1)
                    ->collapsed()
                 /*   ->default(function($get){
                        if(is_null($get("type"))) return null;
                        $type = CustomFieldType::getTypeFromIdentifier($get("type"));
                      //  return $type->mutateCustomFieldDataBeforeFill([""])["options"];
                    })
                    ->visible(function($get, $record){
                       /* if(is_null($get("type"))) return false;
                        $type = CustomFieldType::getTypeFromIdentifier($get("type"));
                        $array = $type->getExtraOptionFields(is_null($record)?new GeneralField():$record);
                        return !empty($array);
                        return true;
                    })
                    ->schema(function($get){
                        if(is_null($get("type"))) return[];
                        $type = CustomFieldType::getTypeFromIdentifier($get("type"));
                        $array = $type->getExtraOptionFields(true);
                        if(empty($array)) return [];
                        $group = Group::make()->schema($array);
                        if($type->getExtraOptionFields(true))  $group->statePath("extra_options");
                        return  [
                            $group
                        ];
                        return [];
                    })*/
                //ToDo add overwrite settings

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('icon')
                    ->label("Icon")
                    ->icon(fn($state)=> $state),
                Tables\Columns\TextColumn::make('name')
                    ->label(__(self::langPrefix . 'label')),

                Tables\Columns\TextColumn::make('type')
                    ->label(__(self::langPrefix .'type'))
                    ->getStateUsing(function (GeneralField $record){
                        return  $record->getType()->getTranslatedName();
                    }),

                Tables\Columns\TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(__(self::langPrefix . 'form_connections'))
                    ->listWithLineBreaks()
                    ->state(fn(GeneralField $record) =>
                        $record->generalFieldForms
                            ->map(fn($generalFieldForm) => $generalFieldForm->dynamicFormConfiguration())
                            ->map(fn(DynamicFormConfiguration $class) => ($class)::displayName())
                    ),

                Tables\Columns\ToggleColumn::make('is_active')
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
