<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Schemas\CustomFormSchema;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\CreateTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\EditTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\ListTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TemplateResource extends Resource
{
//    use Translatable;

    protected static ?string $model = CustomForm::class;

    public static function getNavigationLabel(): string
    {
        return CustomForm::__('label.templates');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return CustomForm::__('label.templates');
    }

    public static function getNavigationGroup(): ?string
    {
        return CustomForm::__('navigation.group-template');
    }

    public static function getNavigationParentItem(): ?string
    {
        $title = CustomForm::__('navigation.parent-template');

        return empty($title) ? null : $title;
    }

    public static function getTitleCaseModelLabel(): string
    {
        return CustomForm::__('label.template');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('template_identifier');
    }

    public static function form(Schema $schema): Schema
    {
        return CustomFormSchema::configure($schema, true);
    }

    public static function table(Table $table): Table
    {
        return CustomFormResource::table($table);
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('showTemplateResource');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTemplate::route('/'),
            'create' => CreateTemplate::route('/create'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }
}
