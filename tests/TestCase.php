<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;


#[WithConfig('database.default', 'mysql')]
#[WithMigration]
abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use WithWorkbench;
    protected $enablesPackageDiscoveries = true;



    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('vendor:publish --all --tag=migrations');
        Artisan::call('filament:assets');
//        Artisan::call('vendor:publish');
//        dump(Artisan::output());
//        rmdir(database_path('migrations'));
//        dump(database_path('migrations'));
    }

    protected function getPackageProviders($app)
    {
        return [
            'Ffhs\FilamentPackageFfhsCustomForms\FilamentPackageFfhsCustomFormsServiceProvider',
        ];
    }

}
