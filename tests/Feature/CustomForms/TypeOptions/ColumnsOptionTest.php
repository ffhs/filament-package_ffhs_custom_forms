<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\TypeOptions\HasTypeOptionEasyTest;
use Filament\Forms\Components\Component;


uses(HasTypeOptionEasyTest::class);

beforeEach(function () {
    $this->typeOptionTestBeforeEach();
});

afterEach(function () {
    $this->typeOptionTestAfterEach();
});


test('fields has columns type option', function ($customFieldIdentifier, array $extraOptions = []) {
    $defaultColumns = CustomFieldType::getTypeFromIdentifier($customFieldIdentifier)->getFlattenExtraTypeOptions(
    )['columns']->getDefaultValue() ?? 1;
    $columns = 3;

    $checkNoOptionFunction = function (Component $component) use ($defaultColumns) {
        expect($component)->not()->toBeNull()
            ->and($component->getColumns()['lg'])->toBe($defaultColumns);
    };

    $checkOptionFunction = function (Component $component) use ($columns) {
        expect($component)->not()->toBeNull()
            ->and($component->getColumns()['lg'])->toBe($columns);
    };

    $this->testComponent(
        $customFieldIdentifier,
        $extraOptions,
        ['columns' => $columns],
        $checkNoOptionFunction,
        $checkOptionFunction
    );
})->with([
    //LayoutType
    [GroupType::identifier(), []],
    [SectionType::identifier(), []],
    [FieldsetType::identifier(), []],

    //OptionType
    [CheckboxListType::identifier(), []],
    [ToggleButtonsType::identifier(), []],
    [ToggleButtonsType::identifier(), ['boolean' => true]],

    //Split
    [RepeaterLayoutType::identifier(), []],
]);
