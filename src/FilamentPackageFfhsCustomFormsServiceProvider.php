<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentPackageFfhsCustomFormsServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void {
        $package
            ->hasMigrations(['create_custom_field_table', 'create_general_field_form_table'])
            ->name('filament-package_ffhs_custom_forms')
            ->hasConfigFile('ffhs_custom_forms')
            ->hasTranslations();

    }

    public function boot(): void {
        parent::boot();
        Factory::guessFactoryNamesUsing(function(string $modelName) {
            return 'Ffhs\\FilamentPackageFfhsCustomForms\\Models\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }

}


