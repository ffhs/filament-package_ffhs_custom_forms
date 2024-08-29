<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Provider;

use Carbon\Laravel\ServiceProvider;
use Ffhs\FilamentPackageFfhsCustomForms\Livewire\FormEditor\CustomFieldComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Livewire\FormEditor\CustomFieldNameComponent;
use Livewire\Livewire;

class CustomFormServiceProvider extends ServiceProvider
{
    public function boot(): void {
        Livewire::component('custom-field', CustomFieldComponent::class);
        #Livewire::component('custom-field-name', CustomFieldNameComponent::class);
    }


}
