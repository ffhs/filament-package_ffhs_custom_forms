<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Commands\Install;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentPackageFfhsCustomFormsServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void {
        $package
            ->name('filament-package_ffhs_custom_forms')
            ->hasMigrations([
                'create_custom_forms_table',
                'create_general_fields_table',
                'create_custom_fields_table',
                'create_general_field_form_table',
                'create_custom_form_answers_table',
                'create_custom_field_answers_table',
                'create_custom_options_table',
                'create_option_general_field_table',
            ])
            ->hasConfigFile('ffhs_custom_forms')
            ->hasTranslations()
            ->hasInstallCommand(fn(InstallCommand $command) =>
                $command
                    ->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->endWith(function (InstallCommand $command) {
                        $command->info("Clear cache and icon cache");
                        // Clear the application cache
                        Artisan::call('icons:cache');
                        Artisan::call('cache:clear');
                    })
            );

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


