<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentPackageFfhsCustomFormsServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void {
        $package
            ->name('filament-package_ffhs_custom_forms')
            ->hasMigrations();
            /*->hasRoutes('web')
            ->hasConfigFile('ffhs_custom_forms')
            ->hasTranslations();*/

    }

}


