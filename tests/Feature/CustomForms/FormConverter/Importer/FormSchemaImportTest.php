<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
});

test('Make FormSchemaImporter without an Error', function () {
    $importer = FormSchemaImporter::make();
    expect($importer)
        ->not->toBeNull()
        ->and($importer)
        ->toBeInstanceOf(FormSchemaImporter::class);
});





test('Import template information\'s', function () {

});



test('Import field information\'s', function () {
});




test('Import rule information\'s', function () {
});


test('Import form', function () {
});

test('Import template', function () {
});

