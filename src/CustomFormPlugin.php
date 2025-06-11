<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CustomFormPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'custom-forms';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                CustomFormResource::class,
                GeneralFieldResource::class,
                TemplateResource::class,
                CustomFormAnswerResource::class,
            ]);
    }

    public function packageBooted(): void
    {
    }

    public function boot(Panel $panel): void
    {
    }
}


