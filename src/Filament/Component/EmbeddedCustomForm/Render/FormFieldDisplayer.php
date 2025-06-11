<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;

class FormFieldDisplayer implements FieldDisplayer
{
    public function __construct()
    {
    }

    public static function make(CustomForm $form): static
    {
        return app(__CLASS__, ['form' => $form]);
    }

    public function __invoke(string $viewMode, CustomField $customField, array $parameter): Component
    {
        return $customField
            ->getType()
            ->getFormComponent($customField, $viewMode, $parameter);
    }
}
