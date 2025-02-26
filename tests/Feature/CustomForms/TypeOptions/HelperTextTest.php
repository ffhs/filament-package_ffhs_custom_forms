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
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\TypeOptions\HasTypeOptionEasyTest;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasHelperText;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportTesting\Testable;


uses(HasTypeOptionEasyTest::class);

beforeEach(function () {
    $this->typeOptionTestBeforeEach();
});

afterEach(function () {
    $this->typeOptionTestAfterEach();
});


test('field has helper text in livewire', function ($customFieldIdentifier, array $extraOptions = []) {
    $helpText = 'Test-Helper-text ' . uniqid();

    $checkNoOptionFunction = function (Testable $livewire) use ($helpText) {
        $livewire->assertSeeText('test_field');
        $livewire->assertDontSee($helpText);
    };

    $checkOptionFunction = function (Testable $livewire) use ($helpText) {
        $livewire->assertSeeText('test_field');
        $livewire->assertSeeText($helpText);
    };

    $this->testFieldLivewire(
        $customFieldIdentifier,
        $extraOptions,
        ['helper_text' => $helpText],
        $checkNoOptionFunction,
        $checkOptionFunction
    );
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
])->only();


test('field has helper text in component', function ($customFieldIdentifier, array $extraOptions = []) {
    $helpText = 'Test-Helper-text ' . uniqid();

    $checkNoOptionFunction = function (Component|HasHelperText $component) {
        expect($component->getHelperText())->toBeNull();
    };

    $checkOptionFunction = function (Component $component) use ($helpText) {
        expect($component->getHelperText())->not()->toBeNull()
            ->and($component->getHelperText())->toBeInstanceOf(HtmlString::class)
            ->and($component->getHelperText()->toHtml())->toBe($helpText);
    };

    $this->testComponent(
        $customFieldIdentifier,
        $extraOptions,
        ['helper_text' => $helpText],
        $checkNoOptionFunction,
        $checkOptionFunction
    );
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
