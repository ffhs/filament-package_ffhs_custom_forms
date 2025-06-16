<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestCustomFormConfiguration;


uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
    $this->testTemplate->save();
    $this->testGeneral->save();
});

test('Import form information\'s', function () {
    $importer = FormSchemaImporter::make();
    $customForm = CustomForm::create(['custom_form_identifier' => TestCustomFormConfiguration::identifier()]);

    //dd(CustomForm::all()->pluck('short_title'));
    $templateMap = CustomForm::query()
        ->whereNot('template_identifier')
        ->pluck('id', 'template_identifier')
        ->toArray();

    $generalFieldMap = GeneralField::query()
        ->pluck('id', 'identifier')
        ->toArray();

    $rawFields = $importer->importFieldDatas(
        $this->exportedFieldInformation,
        customForm: $customForm,
        templateMap: $templateMap,
        generalFieldMap: $generalFieldMap
    );

    $actualOptions = array_map(function ($item) {
        unset($item['custom_form_id']);
        unset($item['template_id']);
        unset($item['general_field_id']);
        if (key_exists('customOptions', $item)) {
            $item['customOptions'] = collect($item['customOptions'])->toArray();
        }
        return $item;
    }, $rawFields);

    expect($actualOptions)
        ->toMatchArray($this->exportedFlattenFieldInformation);
});

test('Import form information\'s to models', function () {
    $importer = FormSchemaImporter::make();
    $customForm = CustomForm::create(['custom_form_identifier' => TestCustomFormConfiguration::identifier()]);

    $importer->importFields(
        $this->exportedFieldInformation,
        customForm: $customForm,
    );

    $generatedFields = CustomField::query()->where('custom_form_id', $customForm->id)->get();
    $generatedOptions = $generatedFields->map(
        fn(CustomField $field) => $field->customOptions()->get()
    )->flatten(1);

    expect($generatedFields)
        ->toHaveCount(count($this->exportedFlattenFieldInformation))
        ->and($generatedOptions)
        ->toHaveCount(2);
});
