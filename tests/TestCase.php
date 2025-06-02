<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests;

use Filament\Facades\Filament;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\App\FFHs\TestCustomFormConfiguration;
use Workbench\Database\Seeders\DatabaseSeeder;


//#[WithConfig('database.default', 'mysql')]
#[WithMigration]
abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected $enablesPackageDiscoveries = true;
    protected $seeder = DatabaseSeeder::class;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('ffhs_custom_forms.forms', [TestCustomFormConfiguration::class]);


        $loader = AliasLoader::getInstance();
        $loader->alias('App\Models\User', 'Workbench\App\Models\User');

    }


    protected function setUp(): void
    {
        // Code before application created.
        $this->afterApplicationCreated(function () {
//            Artisan::call('vendor:publish --tag="filament-package_ffhs_custom_forms-migrations"');
            Artisan::call('filament:assets');

            Filament::setCurrentPanel(
                Filament::getPanel('admin'), // Where `app` is the ID of the panel you want to test.
            );


        });

        parent::setUp();
    }
}
