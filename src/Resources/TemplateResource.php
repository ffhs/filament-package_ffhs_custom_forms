<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages\CreateTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages\EditTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages\ListTemplate;
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
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }
    public static function getNavigationParentItem(): ?string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }
    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.templates');
    }

    public static function getTitleCasePluralModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.templates');
    }

    public static function getTitleCaseModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.form.template');
    }


    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()
            ->where("is_template", true);
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



    public static function getPages(): array
    {
        return [
            'index' => ListTemplate::route('/'),
            'create' => CreateTemplate::route('/create'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }
}
