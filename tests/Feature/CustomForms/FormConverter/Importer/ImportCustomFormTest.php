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
    $form = $importer->importCustomForm($this->exportedFormInformations, [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe($this->exportedFormInformations['short_title']);

    /**@var CustomForm $form*/
    $form = $importer->importCustomForm($this->exportedFormInformations, ['short_title' => 'hallo'], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe('hallo');

    /**@var CustomForm $form*/
    $form = $importer->importCustomForm($this->exportedTemplateInformations, [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeTrue()
        ->and($form->short_title)->toBe($this->exportedTemplateInformations['short_title']);
});

