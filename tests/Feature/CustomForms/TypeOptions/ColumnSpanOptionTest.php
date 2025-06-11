<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
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
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\TypeOptions\HasTypeOptionEasyTest;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Filament\Forms\Components\Component;


uses(HasTypeOptionEasyTest::class);

beforeEach(function () {
    $this->typeOptionTestBeforeEach();
});

afterEach(function () {
    $this->typeOptionTestAfterEach();
});

test('column span modify settings component', function () {
    $component = ColumnSpanOption::make()->getModifyOptionComponent('column_span');
    expect($component)->toBeInstanceOf(Component::class)
        ->and($component->getStatePath(false))->toBe('column_span');
});


test('field has column span in component', function ($customFieldIdentifier, array $extraOptions = []) {
    $defaultColumns = CustomFieldType::getTypeFromIdentifier($customFieldIdentifier)->getFlattenExtraTypeOptions()['column_span']->getDefaultValue() ?? 1;
    $columnSpan = 1;

    $checkNoOptionFunction = function (Component $component) use ($defaultColumns) {
        expect($component)->not()->toBeNull()
            ->and($component->getColumnSpan()['default'])->toBe($defaultColumns);
    };

    $checkOptionFunction = function (Component $component) use ($columnSpan) {
        expect($component)->not()->toBeNull()
            ->and($component->getColumnSpan()['default'])->toBe($columnSpan);
    };

    $this->componentTestField(
        $customFieldIdentifier,
        $extraOptions,
        ['column_span' => $columnSpan],
        $checkNoOptionFunction,
        $checkOptionFunction
    );
})->with([
    //Generic Types
//    [CheckboxType::identifier(), []], Has no columSpan

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

    //Layout Type
    [DownloadType::identifier(), []],
    [FieldsetType::identifier(), []],
    [ImageLayoutType::identifier(), []],
    [SectionType::identifier(), []],
//    [SpaceType::identifier(), []], // Has no columSpan
    [TextLayoutType::identifier(), []],
    [TitleType::identifier(), []],

    //Split
    [RepeaterLayoutType::identifier(), []],
]);
