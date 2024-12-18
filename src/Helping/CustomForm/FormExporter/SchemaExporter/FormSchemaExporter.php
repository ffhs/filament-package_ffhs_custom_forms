<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Support\Facades\App;

class FormSchemaExporter
{

    public static function make(): static
    {
        return App::make(static::class);
    }

    public function export(CustomForm $form): array{
        return [];
    }

    public function import(array $rawForm): CustomForm{
        return new CustomForm();
    }

}
