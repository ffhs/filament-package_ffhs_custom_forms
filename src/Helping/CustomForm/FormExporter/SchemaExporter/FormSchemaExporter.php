<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

class FormSchemaExporter
{

    public static function make(): static
    {
        return app(static::class);
    }

    public function export(CustomForm $form): array{
        return [];
    }

    public function import(array $rawForm): CustomForm{
        return new CustomForm();
    }

}
