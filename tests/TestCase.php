<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\Database\Seeders\DatabaseSeeder;


#[WithConfig('database.default', 'mysql')]
#[WithMigration]
abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use WithWorkbench;
    protected $enablesPackageDiscoveries = true;
    protected $seeder = DatabaseSeeder::class;


    protected function setUp(): void
    {
        // Code before application created.

        $this->afterApplicationCreated(function () {
//            Artisan::call('vendor:publish --tag="filament-package_ffhs_custom_forms-migrations"');
            Artisan::call('filament:assets');
        });

//        $this->beforeApplicationDestroyed(function () {
//            $path = database_path('migrations');
//            system("rm -rf ". escapeshellarg($path));
//            mkdir($path);
//        });
        parent::setUp();
    }


}
