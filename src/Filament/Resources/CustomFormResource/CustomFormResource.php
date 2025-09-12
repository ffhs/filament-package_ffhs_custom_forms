<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Schemas\CustomFormSchema;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class CustomFormResource extends Resource
{
    use Translatable;

    protected static ?string $model = CustomForm::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationLabel(): string
    {
        return CustomForm::__('label.multiple');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return CustomForm::__('label.multiple');
    }

    public static function getTitleCaseModelLabel(): string
    {
        return CustomForm::__('label.single');
    }

    public static function getNavigationGroup(): ?string
    {
        return CustomForm::__('navigation.group');
    }

    public static function getNavigationParentItem(): ?string
    {
        $title = CustomForm::__('navigation.parent');

        return empty($title) ? null : $title;
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('showResource');
    }

    public static function form(Schema $schema): Schema
    {
        return CustomFormSchema::configure($schema, false);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('template_identifier');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomForm::route('/'),
            'create' => CreateCustomForm::route('/create'),
            'edit' => EditCustomForm::route('/{record}/edit'),
        ];
    }
}
