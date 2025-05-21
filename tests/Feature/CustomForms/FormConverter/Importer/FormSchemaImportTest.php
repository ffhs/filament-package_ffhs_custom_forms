<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FormImportException;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConverter\FormImporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestDynamicFormConfiguration;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
    $this->testTemplate->save();
    $this->testGeneral->save();


    $this->formData = [
        "form" => $this->exportedFormInformation,
        "fields" => $this->exportedFieldInformation,
        "rules" => $this->exportedRuleInformation,
    ];
});

test('Make FormSchemaImporter without an Error', function () {
    $importer = FormSchemaImporter::make();
    expect($importer)
        ->not->toBeNull()
        ->and($importer)
        ->toBeInstanceOf(FormSchemaImporter::class);
});


test('Import Form', function () {
    $importer = FormSchemaImporter::make();
    $countFormsBefore = CustomForm::query()->count();
    $form = $importer->import($this->formData, new TestDynamicFormConfiguration());
    expect(CustomForm::query()->count())->toBe($countFormsBefore + 1)
        ->and($form)->not->toBeNull();
});

test('Import Form with overwritten template', function () {

    $testOverwrittenTemplate = CustomForm::create([
        'short_title' => 'My custom template title 2',
        'template_identifier' => 'my_template2',
        'custom_form_identifier' => 'test_form_identifier2',
    ]);

    $templateMap = [
        $this->testTemplate->template_identifier => $testOverwrittenTemplate->id
    ];

    $importer = FormSchemaImporter::make();
    $countFormsBefore = CustomForm::query()->count();
    $form = $importer->import(
        $this->formData,
        configuration: new TestDynamicFormConfiguration(),
        templateMap: $templateMap
    );

    expect(CustomForm::query()->count())->toBe($countFormsBefore + 1)
        ->and($form)->not->toBeNull();

    $templateField = $form->customFields()
        ->firstWhere('template_id', $testOverwrittenTemplate->id);

    expect($templateField)->not->toBeNull();
});

test('Import Form with overwritten generalfield', function () {
    $testOverwrittenGeneralField = GeneralField::create([
        'name' => ['de' => 'test generales Feld 2', 'en' => 'test general field 2'],//['de' => 'test generales Feld', 'en' => 'test general field'],
        'identifier' => 'test_general2',
        'type' => TextType::identifier(),
        'icon' => 'test-icon',
        'options' => ['required' => true],
    ]);

    $generalMap = [
        $this->testGeneral->identifier => $testOverwrittenGeneralField->id
    ];

    $importer = FormSchemaImporter::make();
    $countFormsBefore = CustomForm::query()->count();
    $form = $importer->import(
        $this->formData,
        configuration: new TestDynamicFormConfiguration(),
        generalFieldMap: $generalMap
    );

    expect(CustomForm::query()->count())->toBe($countFormsBefore + 1)
        ->and($form)->not->toBeNull();

    $templateField = $form->customFields()
        ->firstWhere('general_field_id', $testOverwrittenGeneralField->id);

    expect($templateField)->not->toBeNull();
});


test('Import Form with an error and rollback', function () {
    $importer = FormSchemaImporter::make();
    $countFormsBefore = CustomForm::query()->count();
    try {
        $importer->import([
            "form" => $this->exportedFormInformation,
            "fields" => [
                'coffee' => 'programmer'
            ]
        ], new TestDynamicFormConfiguration());

        // If no exception is thrown, fail the test
        expect(false)->toBe(true, 'no exception');
    } catch (FormImportException) {}


    expect(CustomForm::query()->count())->toBe($countFormsBefore);

});


test('Import Form on existing Form', function () {
    $importer = FormSchemaImporter::make();
    $form = CustomForm::create(
        ['short_title' => 'test_2',
            'custom_form_identifier' => TestDynamicFormConfiguration::identifier()
        ]);
    $formsCountBefore= CustomForm::query()->count();
    $form = $importer->importWithExistingForm($this->formData, $form);
    expect($form)->not->toBeNull()
        ->and($form->ownedFields)->toHaveCount(count($this->exportedFlattenFieldInformation))
        ->and(CustomForm::query()->count())->toBe($formsCountBefore);
});


test('Import Form on existing Form with existing Fields', function () {

})->todo();



