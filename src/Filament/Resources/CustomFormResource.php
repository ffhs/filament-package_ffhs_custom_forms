<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomFormPolicy;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class CustomFormResource extends Resource
{
    use Translatable;


    protected static ?string $model = CustomForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected array $policies = [
        CustomForm::class => CustomFormPolicy::class,
    ];

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }


    public static function getTitleCasePluralModelLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }

    public static function getTitleCaseModelLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.form.custom_form');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('template_identifier');
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('showResource');
    }


    public static function getPages(): array
    {
        return [
            'index' => ListCustomForm::route('/'),
            'create' => CreateCustomForm::route('/create'),
            'edit' => EditCustomForm::route('/{record}/edit'),
        ];
    }


    private function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
