<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
});


test('Export form information\'s', function () {
    $exporter = FormSchemaExporter::make();

    expect($exporter->exportFormInformation($this->testForm))
        ->toEqual($this->exportedFormInformation)
        ->and($exporter->exportFormInformation($this->testTemplate))
        ->toEqual($this->exportedTemplateInformation);
});




