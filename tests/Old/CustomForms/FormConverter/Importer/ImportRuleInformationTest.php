<?php

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConverter\FormSchemaImporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Workbench\App\FFHs\TestCustomFormConfiguration;

uses(FormConverterCase::class);

beforeEach(function () {
    $this->initialize();
    $this->setUpTestImportedSchema();
    $this->testTemplate->save();
    $this->testGeneral->save();
});

test('Import triggers and events', function () {

    $importer = FormSchemaImporter::make();
    foreach ($this->exportedRuleInformation as $ruleData) {
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
    /**@var CustomForm $customForm */
    $customForm = CustomForm::create(['custom_form_identifier' => TestCustomFormConfiguration::identifier()]);
    $importer->importRule($this->exportedRuleInformation, $customForm);

    $createdRules = $customForm->ownedRules;

    expect($createdRules)
        ->toHaveCount(count($this->exportedRuleInformation));
});

