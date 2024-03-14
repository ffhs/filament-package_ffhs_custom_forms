<?php

namespace Ffhs\FilamentPackageFfhsCustomForms;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\Factory;

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
                FormVariationResource::class,
                CustomFormAnswerResource::class,
            ]);
    }


    public function boot(Panel $panel): void {

    }
}


