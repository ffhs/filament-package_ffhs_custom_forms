<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\FormSchemaExporter;


test('Make FormSchemaExporter without an Error', function () {
    $exporter = FormSchemaExporter::make();
    expect($exporter)
        ->not->toBeNull()
        ->and($exporter)->toBeInstanceOf(FormSchemaExporter::class);
});
