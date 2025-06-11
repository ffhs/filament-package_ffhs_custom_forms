<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\FormImporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestCustomFormConfiguration;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
});


test('Import form information\'s', function () {
    $importer = FormSchemaImporter::make();
    $config = new TestCustomFormConfiguration();

    /**@var CustomForm $form */
    $form = $importer->importCustomForm(['form' => $this->exportedFormInformation], [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe($this->exportedFormInformation['short_title']);

    /**@var CustomForm $form */
    $form = $importer->importCustomForm(['form' => $this->exportedFormInformation], ['short_title' => 'hallo'],
        $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeFalse()
        ->and($form->short_title)->toBe('hallo');

    /**@var CustomForm $form */
    $form = $importer->importCustomForm(['form' => $this->exportedTemplateInformation], [], $config);
    expect($form)->not->toBeNull()
        ->and($form->is_template)->toBeTrue()
        ->and($form->short_title)->toBe($this->exportedTemplateInformation['short_title']);
});

