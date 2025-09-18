<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Illuminate\Support\Collection;


interface EmbedCustomForm
{
    public function getFormConfiguration(): CustomFormConfiguration;

    public function getOwnedFields(): Collection;

    public function customFields(): Collection;

    public function getRules(): Collection;
}
