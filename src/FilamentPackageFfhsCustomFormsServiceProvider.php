<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
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
            $factoryNames = [
                CustomField::class,
                CustomFieldAnswer::class,
                CustomFieldVariation::class,
                CustomForm::class,
                CustomFormAnswer::class,
                FormVariation::class,
                GeneralField::class,
                GeneralFieldForm::class
            ];

            if(in_array($modelName,$factoryNames))
                return 'Ffhs\\FilamentPackageFfhsCustomForms\\Models\Factories\\' . class_basename($modelName) . 'Factory';
            else return 'Database\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }

}


