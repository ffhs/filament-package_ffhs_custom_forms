<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestDynamicFormConfiguration;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
});




test('Import form information\'s', function () {
    $importer = FormSchemaImporter::make();
    $config = new TestDynamicFormConfiguration();

    /**@var CustomForm $form*/
    $form = $importer->importCustomForm($this->exportedFormInformation, [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe($this->exportedFormInformation['short_title']);

    /**@var CustomForm $form*/
    $form = $importer->importCustomForm($this->exportedFormInformation, ['short_title' => 'hallo'], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe('hallo');

    /**@var CustomForm $form*/
    $form = $importer->importCustomForm($this->exportedTemplateInformation, [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeTrue()
        ->and($form->short_title)->toBe($this->exportedTemplateInformation['short_title']);
});

