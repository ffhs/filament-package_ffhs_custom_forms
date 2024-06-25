<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Livewire\FormEditor\CustomFieldComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
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
                'create_option_custom_field_table',
                'create_field_rules_table',
            ])
            ->hasConfigFile('ffhs_custom_forms')
            ->hasTranslations()
            ->hasInstallCommand(fn(InstallCommand $command) =>
                $command
                    ->startWith(function (InstallCommand $command){
                        $command->info("Publish translation from Filament\Spatie-Translatable");
                       // Artisan::call('vendor:publish', ["tag" => "filament-spatie-laravel-translatable-plugin-translations"]); ToDo repair

                        $command->info("Publish config from icon picker plugin");
                        // Artisan::call('vendor:publish', ["tag" => "filament-icon-picker-config"]); ToDo repair
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->copyAndRegisterServiceProviderInApp()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->endWith(function (InstallCommand $command) {
                        // Clear the application cache
                        $command->info("Clear cache");
                        Artisan::call('cache:clear');

                        $command->info("Clear icon cache");
                        Artisan::call('icons:cache');

                        $command->info("Create storage symlink");
                        Artisan::call('storage:link');
                    })
            )
            ->hasViews('filament-package_ffhs_custom_forms');


    }

    public function boot(): void {
        parent::boot();
        Factory::guessFactoryNamesUsing(function(string $modelName) {
            $factoryNames = [
                CustomField::class,
                CustomFieldAnswer::class,
                CustomForm::class,
                CustomFormAnswer::class,
                GeneralField::class,
                GeneralFieldForm::class
            ];

            if(in_array($modelName,$factoryNames))
                return 'Ffhs\\FilamentPackageFfhsCustomForms\\Models\Factories\\' . class_basename($modelName) . 'Factory';
            else return 'Database\Factories\\' . class_basename($modelName) . 'Factory';
        });

        FilamentAsset::register([
            Js::make('custom_form_script', __DIR__ . '/../resources/js/custom_form_script.js')->loadedOnRequest(),
        ], 'ffhs/filament-package_ffhs_custom_forms');

        $this->publishes([
            __DIR__.'/../resources/js/custom_form_script.js' => public_path('js/ffhs/'.$this->package->name.'/custom_form_script.js'),
        ], 'filament-package_ffhs_custom_forms-assets');

    }

}


