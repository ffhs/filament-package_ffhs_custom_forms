<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
});


test('Make FormSchemaExporter without an Error', function () {
    $exporter = FormSchemaExporter::make();
    expect($exporter)
        ->not->toBeNull()
        ->and($exporter)->toBeInstanceOf(FormSchemaExporter::class);
});


test('Export form', function () {
    $exporter = FormSchemaExporter::make();
    expect($exporter->export($this->testForm))
        ->toEqual([
            'form' => $this->exportedFormInformation,
            'fields' => $this->exportedFieldInformation,
            'rules' => $this->exportedRuleInformation,
        ]);
});

test('Export Template', function () {
    $exporter = FormSchemaExporter::make();
    expect($exporter->export($this->testForm))
        ->toEqual([
            'form' => $this->exportedFormInformation,
            'fields' => $this->exportedFieldInformation,
            'rules' => $this->exportedRuleInformation,
        ]);
});

