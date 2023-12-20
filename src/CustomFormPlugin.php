<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormsResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CustomFormPlugin implements Plugin {

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string {
        return 'sso';
    }


    public function register(Panel $panel): void {
        $panel
            ->resources([
                CustomFormsResource::class,
                GeneralFieldResource::class
            ]);
    }


    public function boot(Panel $panel): void {
        //
    }
}


