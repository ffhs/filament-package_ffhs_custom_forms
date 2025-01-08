<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleEvent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestDynamicFormConfiguration;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
    $this->testTemplate->save();
    $this->testGeneral->save();
});

test('Import triggers and events', function () {

    $importer = FormSchemaImporter::make();
    foreach ($this->expordetRuleInformations as $ruleData){
        $rule = Rule::create(['is_or_mode' => false]);
        $importer->importRuleElements($rule, $ruleData);

        $triggers = RuleTrigger::query()->where([
            'rule_id' => $rule->id,
        ])->count();
        $events = RuleEvent::query()->where([
            'rule_id' => $rule->id,
        ])->count();

        expect($events)->toEqual(count($ruleData['events'] ?? []))
            ->and($triggers)->toEqual(count($ruleData['triggers'] ?? []));
    }
});


test('Import form rule\'s', function () {
    $importer = FormSchemaImporter::make();
    /**@var CustomForm $customForm*/
    $customForm = CustomForm::create(['custom_form_identifier' => TestDynamicFormConfiguration::identifier()]);
    $importer->importRule($this->expordetRuleInformations,$customForm);

    $createdRules = $customForm->ownedRules;

    expect($createdRules)
        ->toHaveCount(count($this->expordetRuleInformations));
});

