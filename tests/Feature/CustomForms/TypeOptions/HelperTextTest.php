<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\DateRangeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\FileUploadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Support\Facades\Artisan;
use Workbench\App\FFHs\TestDynamicFormConfiguration;
use Workbench\App\Models\UserSuperAdmin;

use function Pest\Livewire\livewire;


beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234',
    ]);
    $this->actingAs($user);

    /**@var CustomForm $customForm */
    $this->customForm = CustomForm::create([
        'short_title' => 'My custom form title',
        'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
    ]);

    $this->formAnsware = CustomFormAnswer::create([
        'custom_form_id' => $this->customForm->id,
        'short_title' => 'test answare',
    ]);
});

afterEach(function () {
    $this->customField->delete();
    $this->customForm->refresh();
    $this->formAnsware->refresh();
});

test('field show helper text', function ($customFieldIdentifier, array $extraOptions = []) {
    $helpText = 'Test-Helper-text';

    $this->customField = CustomField::create([
        'name' => ['en' => 'test_field'],
        'form_position' => 1,
        'layout_end_position' => 1,
        'identifier' => uniqid(),
        'type' => $customFieldIdentifier,
        'custom_form_id' => $this->customForm->id,
        'options' => $extraOptions,
    ]);

    $this->customForm->refresh();

    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertSuccessful();
    $livewire->assertSeeText('test_field');
    $livewire->assertDontSee($helpText);

    $this->customField->update(['options' => array_merge($extraOptions, ['helper_text' => $helpText])]);
    Artisan::call('cache:clear');
    $this->customField->refresh();
    $this->customForm->refresh();
    $this->formAnsware->refresh();

    $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnsware->id]);
    $livewire->assertSuccessful();
    $livewire->assertSeeText('test_field');
    $livewire->assertSeeText($helpText);
})->with([
//    //Generic Types
    [CheckboxType::identifier(), []],
    [ColorPickerType::identifier(), []],
    [DateRangeType::identifier(), []],
    [DateTimeType::identifier(), []],
    [EmailType::identifier(), []],
    [FileUploadType::identifier(), []],
    [IconSelectType::identifier(), []],
    [KeyValueType::identifier(), []],
    [NumberType::identifier(), []],
    [TagsType::identifier(), []],
    [TextAreaType::identifier(), []],
    [TextType::identifier(), []],

    //OptionType
    [CheckboxListType::identifier(), []],
    [RadioType::identifier(), []],
    [SelectType::identifier(), []],
    [SelectType::identifier(), ['several' => true]],
    [SelectType::identifier(), ['prioritized' => true]],
    [ToggleButtonsType::identifier(), []],
    [ToggleButtonsType::identifier(), ['boolean' => true]],

    //Split
    [RepeaterLayoutType::identifier(), []],
]);
