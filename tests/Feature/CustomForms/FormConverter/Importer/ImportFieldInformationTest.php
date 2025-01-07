<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
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
});

test('Import form information\'s', function () {
    $importer = FormSchemaImporter::make();
    $customForm = CustomForm::create(['custom_form_identifier' => TestDynamicFormConfiguration::identifier()]);

    //dd(CustomForm::all()->pluck('short_title'));
    $templateMap = CustomForm::query()
        ->whereNot("template_identifier")
        ->pluck('id', 'template_identifier')
        ->toArray();

    $generalFieldMap = GeneralField::query()
        ->pluck('id', 'identifier')
        ->toArray();

    $rawFields = $importer->importFields(
        $this->expordetFieldInformations,
        customForm: $customForm,
        templateMap: $templateMap,
        generalFieldMap: $generalFieldMap
    );

    $actualOptions = array_map(function($item) {
        unset($item['custom_form_id']);
        unset($item['template_id']);
        unset($item['general_field_id']);
        return $item;
    }, $rawFields);

    expect($actualOptions)
        ->toMatchArray($this->expordetFlattenFieldInformations);
});

test('Import form information\'s to models', function () {
    $importer = FormSchemaImporter::make();
    $customForm = CustomForm::create(['custom_form_identifier' => TestDynamicFormConfiguration::identifier()]);

    $importer->createImportFields(
        $this->expordetFieldInformations,
        customForm: $customForm,
    );

    $generatedFields = CustomField::query()->where('custom_form_id', $customForm->id)->get();


    expect($generatedFields)
        ->toHaveCount(count($this->expordetFlattenFieldInformations));
});
