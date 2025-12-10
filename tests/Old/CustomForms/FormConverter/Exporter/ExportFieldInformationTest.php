<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
});

test('Export field information\'s', function () {

    $exporter = FormSchemaExporter::make();
    $export = $exporter->exportFieldInformation($this->testFields);

    expect($export)
        ->toEqual($this->exportedFieldInformation);
});
