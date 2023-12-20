<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentPackageFfhsCustomFormsServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void {
        $package
            ->name('filament-package_ffhs_custom_forms')
            ->hasConfigFile('ffhs_custom_forms')
            ->hasMigrations(['create_custom_field_table', 'create_general_field_form_table']);

            /*->hasRoutes('web')
            ->hasTranslations();*/

    }

}


