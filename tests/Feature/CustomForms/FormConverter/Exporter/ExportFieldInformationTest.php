<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
});

test('Export field information\'s', function () {

    $exporter = FormSchemaExporter::make();
    expect($exporter->exportFieldInformation($this->testFields))
        ->toEqual($this->expordetFieldInformations);
});
