<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\CreateCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ListCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ViewCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Schemas\CustomFormAwareSchema;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Tables\CustomFormAnswerTable;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomFormAnswerResource extends Resource
{
    protected static ?string $model = CustomFormAnswer::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'custom-form-answers';

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name_' . app()->getLocale();
    }

    public static function getNavigationGroup(): ?string
    {
        return CustomFormAnswer::__('navigation.group');
    }

    public static function getNavigationParentItem(): ?string
    {
        $title = CustomFormAnswer::__('navigation.parent');

        return empty($title) ? null : $title;
    }

    public static function getNavigationLabel(): string
    {
        return CustomFormAnswer::__('label.multiple');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return CustomFormAnswer::__('label.multiple');
    }

    public static function getTitleCaseModelLabel(): string
    {
        return CustomFormAnswer::__('label.single');
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('showResource');
    }

    public static function table(Table $table): Table
    {
        return CustomformAnswerTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return CustomFormAwareSchema::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomFormAnswer::route('/'),
            'create' => CreateCustomFormAnswer::route('/create'),
            'edit' => EditCustomFormAnswer::route('/{record}/edit'),
            'view' => ViewCustomFormAnswer::route('/{record}')
        ];
    }


    public static function resolveRecordRouteBinding(string|int $key, ?Closure $modifyQuery = null): ?Model
    {
        $query = static::getRecordRouteBindingEloquentQuery();

        if ($modifyQuery) {
            $query = $modifyQuery($query) ?? $query;
        }

        return app(static::getModel())
            ->resolveRouteBindingQuery($query, $key, static::getRecordRouteKeyName())
            ->with(['customForm', 'customFieldAnswers'])
            ->first();
    }
}
