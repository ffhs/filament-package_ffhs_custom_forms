<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages\{CreateGeneralField,
    EditGeneralField,
    ListGeneralField};
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages\HasGeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\RelationManagers\GeneralFieldFormRelationManager;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneralFieldResource extends Resource
{
    use Translatable;
    use HasGeneralFieldForm;

    public const langPrefix = 'filament-package_ffhs_custom_forms::models.general_field.';
    protected static ?string $model = GeneralField::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'name';


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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label(GeneralField::__('icon.label'))
                    ->icon(fn($state) => $state),
                TextColumn::make('name')
                    ->searchable()
                    ->label(GeneralField::__('name.label')),
                TextColumn::make('type')
                    ->label(GeneralField::__('type.label'))
                    ->searchable()
                    ->getStateUsing(fn(GeneralField $record) => $record->getType()->getTranslatedName()),
                TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(GeneralField::__('form_connections.label'))
                    ->listWithLineBreaks()
                    ->searchable()
                    ->state(fn(GeneralField $record) => $record->generalFieldForms
                        ->map(fn($generalFieldForm) => $generalFieldForm->dynamicFormConfiguration())
                        ->map(fn(DynamicFormConfiguration $class) => ($class)::displayName())
                    ),

                ToggleColumn::make('is_active')
                    ->label(GeneralField::__('is_active.label')),
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
