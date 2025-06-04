<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;


use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\CreateTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\EditTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\ListTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TemplateResource extends Resource
{
    use Translatable;

    protected static ?string $model = CustomForm::class;

    public static function getNavigationGroup(): ?string
    {
        return CustomForms::__('custom_forms.navigation.group.forms');
    }

    public static function getNavigationParentItem(): ?string
    {
        return CustomForms::__('custom_forms.navigation.forms');
    }

    public static function getNavigationLabel(): string
    {
        return CustomForms::__('custom_forms.navigation.templates');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return CustomForms::__('custom_forms.navigation.templates');
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

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return CustomFormResource::table($table);
    }

    public static function getRelations(): array
    {
        return [];
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
