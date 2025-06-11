<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\CheckboxType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\ColorPickerType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\DateRangeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\DateTimeType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\EmailType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\FileUploadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\IconSelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\KeyValueType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\NumberType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TagsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TextAreaType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\TypeOptions\HasTypeOptionEasyTest;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\RequiredOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasHelperText;


uses(HasTypeOptionEasyTest::class);

beforeEach(function () {
    $this->typeOptionTestBeforeEach();
});

afterEach(function () {
    $this->typeOptionTestAfterEach();
});

test('required modify settings component', function () {
    $component = RequiredOption::make()->getModifyOptionComponent('required');
    expect($component)->toBeInstanceOf(Component::class)
        ->and($component->getStatePath(false))->toBe('required');
});


test('field is required in component', function ($customFieldIdentifier, array $extraOptions = []) {
    $checkNoOptionFunction = function (Component|HasHelperText $component) {
        expect($component->isRequired())->toBeFalse();
    };

    $checkOptionFunction = function (Component $component) {
        expect($component->isRequired())->toBeTrue();
    };

    $this->componentTestField(
        $customFieldIdentifier,
        $extraOptions,
        ['required' => true],
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
//    [RepeaterLayoutType::identifier(), []], it Has min_items which is used as required
]);
