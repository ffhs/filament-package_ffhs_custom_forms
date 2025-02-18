<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomFieldPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomFormPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomOptionPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\GeneralFieldPolicy;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentPackageFfhsCustomFormsServiceProvider extends PackageServiceProvider
{

    protected array $policies = [
        CustomForm::class => CustomFormPolicy::class,
        CustomField::class => CustomFieldPolicy::class,
        GeneralField::class => GeneralFieldPolicy::class,
        CustomOption::class => CustomOptionPolicy::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-package_ffhs_custom_forms')
            ->hasMigrations([
                'create_custom_forms_tables',
                'create_custom_fields_tables',
                'create_custom_options_tables',
                'create_rules_tables',
                'create_form_relations_tables',
                'seed_custom_forms_permissions'
            ])
            ->hasConfigFile('ffhs_custom_forms')
            ->hasTranslations()
            ->hasInstallCommand(fn(InstallCommand $command) => $command
                ->startWith(function (InstallCommand $command) {
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

    public function boot(): void
    {
        parent::boot();
        $this->registerPolicies();
        $this->registerFilamentAssets();

        //        $this->publishes([
//            __DIR__.'/../resources/js/dropping.js' => public_path('js/ffhs/'.$this->package->name.'/dropping.js'),
//        ], 'filament-package_ffhs_custom_forms-assets');

    }

    private function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * @return void
     */
    public function registerFilamentAssets(): void
    {
//Drag and Drop
        FilamentAsset::register([
            AlpineComponent::make('parent', __DIR__ . '/../dist/js/drag-drop/parent.js')
                ->loadedOnRequest(),
            AlpineComponent::make('element', __DIR__ . '/../dist/js/drag-drop/element.js')
                ->loadedOnRequest(),
            AlpineComponent::make('container', __DIR__ . '/../dist/js/drag-drop/container.js')
                ->loadedOnRequest(),
            AlpineComponent::make('action', __DIR__ . '/../dist/js/drag-drop/action.js')
                ->loadedOnRequest(),

            Css::make('stylesheet', __DIR__ . '/../dist/css/drag_drop.css')
                ->loadedOnRequest(),
        ], 'ffhs/filament-package_ffhs_drag-drop');
    }

}


