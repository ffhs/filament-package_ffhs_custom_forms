<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

use Filament\Support\Components\Component;

interface FieldTypeView
{
    public static function make(): static;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component;

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component;
}
