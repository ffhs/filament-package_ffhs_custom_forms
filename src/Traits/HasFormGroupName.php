<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

trait HasFormGroupName
{
    public function getGroupName(): string
    {
        return 'custom_fields-' . $this->getFormConfiguration()::identifier();
    }
}
