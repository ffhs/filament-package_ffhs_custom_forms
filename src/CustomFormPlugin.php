<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomFormAnswerPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\CustomFormPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Policies\GeneralFieldPolicy;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Ffhs\FilamentPackageFfhsSso\Models\SsoUser;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

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


    public function packageBooted(): void {
        //Setup Policies
        Gate::policy(SsoUser::class, CustomFormPolicy::class);
        Gate::policy(SsoUser::class, CustomFormAnswerPolicy::class);
        Gate::policy(SsoUser::class, GeneralFieldPolicy::class);
    }

    public function boot(Panel $panel): void {

    }
}


