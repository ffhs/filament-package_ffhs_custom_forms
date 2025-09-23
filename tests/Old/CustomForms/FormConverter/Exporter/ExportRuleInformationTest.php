<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
});

test('Export rule information\'s', function () {
    $exporter = FormSchemaExporter::make();
    expect($exporter->exportRuleInformation($this->testRules))
        ->toEqual($this->exportedRuleInformation);
});
