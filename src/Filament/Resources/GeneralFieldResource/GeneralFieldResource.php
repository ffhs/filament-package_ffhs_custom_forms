<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Pages\{CreateGeneralField,
    EditGeneralField,
    ListGeneralField};
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\RelationManagers\GeneralFieldFormRelationManager;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Schemas\GeneralFieldSchema;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Tables\GeneralFieldTable;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class GeneralFieldResource extends Resource
{
    use Translatable;

//    public const langPrefix = 'filament-package_ffhs_custom_forms::models.general_field.';
    protected static ?string $model = GeneralField::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name.' . app()->getLocale();
    }


    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('filamentResource');
    }

    public static function getNavigationLabel(): string
    {
        return GeneralField::__('label.single');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return GeneralField::__('label.multiple');
    }

    public static function getNavigationGroup(): ?string
    {
        return GeneralField::__('navigation.group');
    }

    public static function getNavigationParentItem(): ?string
    {
        $title = GeneralField::__('navigation.parent');

        return empty($title) ? null : $title;
    }

    public static function getTitleCaseModelLabel(): string
    {
        return GeneralField::__('label.multiple');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('generalFieldForms');
    }

    public static function table(Table $table): Table
    {
        return GeneralFieldTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return GeneralFieldSchema::configure($schema);
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
