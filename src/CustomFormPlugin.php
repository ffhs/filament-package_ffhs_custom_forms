<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CustomFormPlugin implements Plugin {

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string {
        return 'custom-forms';
    }


    public function register(Panel $panel): void {
        $panel
            ->resources([
                CustomFormResource::class,
                GeneralFieldResource::class,
                TemplateResource::class,
                CustomFormAnswerResource::class,
            ]);
    }


    public function boot(Panel $panel): void {

    }
}


