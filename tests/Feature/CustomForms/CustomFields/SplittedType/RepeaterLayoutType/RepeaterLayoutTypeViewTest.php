<?php

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Workbench\App\FFHs\TestDynamicFormConfiguration;
use Workbench\App\Models\UserSuperAdmin;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($user);

    /**@var CustomForm $customForm*/
    $this->customForm = CustomForm::create([
        'short_title' => 'My custom form title',
        'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
    ]);

    $this->customField = CustomField::create([
        'name' => ['de' => 'test_field'],
        'form_position' => 1,
        'layout_end_position' => 1,
        'identifier' => uniqid(),
        'type' => RepeaterLayoutType::identifier(),
        'custom_form_id' => $this->customForm->id,
        'options' => [],
    ]);

    $this->formAnsware = CustomFormAnswer::create([
        'custom_form_id' => $this->customForm->id,
        'short_title' => 'test answare'
    ]);

//    $customForm->ownedFields()->save($customField);
});

test('repeater show label', function () {
    $this->customField->update(['options' => ['show_label' => true]]);

    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertSeeText($this->customField->short_title);

    $this->customField->update(['options' => ['show_label' => false]]);

    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertDontSee($this->customField->short_title);

    expect(true)->toBeTrue();
});


test('repeater show add action label', function () {
    $testLabel = 'add to test';
    $this->customField->update(['options' => ['add_action_label' => 'add to test']]);

    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertSeeText($testLabel);


    $this->customField->update(['options' => ['add_action_label' => null]]);
    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertDontSeeText($testLabel);


    expect(true)->toBeTrue();
});


test('order option', function () {})->todo();
test('min_amount option', function () {})->todo();
test('max_amount option', function () {})->todo();
test('default_amount option', function () {})->todo();
test('render contend', function () {})->todo();
test('render infolist', function () {})->todo();
