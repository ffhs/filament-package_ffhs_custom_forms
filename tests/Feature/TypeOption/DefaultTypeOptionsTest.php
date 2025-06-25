<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\CheckboxListType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\RadioType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\SelectType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\ToggleButtonsType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
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
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\DownloadType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\FieldsetType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\ImageLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\SpaceType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TextLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\TitleType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\TypeOption\HasTypeOptionTestHelper;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ActionLabelTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnsOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\DateFormatOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\HelperTextTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\IconOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ImaginaryTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InLineLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\InlineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxSelectOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinAmountOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinSelectOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinValueOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\RelatedFieldOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ReorderableTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowAsFieldsetOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ValidationMessageOption;
use Filament\Forms\Components\Component;

uses(HasTypeOptionTestHelper::class);

$allTypes = [
    [CheckboxListType::class, []],
    [RadioType::class, []],
    [SelectType::class, []],
    [SelectType::class, ['several' => true]],
    [SelectType::class, ['prioritized' => true]],
    [ToggleButtonsType::class, []],
    [ToggleButtonsType::class, ['boolean' => true]],

    [CheckboxType::class, []],
    [ColorPickerType::class, []],
    [DateRangeType::class, []],
    [DateTimeType::class, []],
    [EmailType::class, []],
    [FileUploadType::class, []],
    [IconSelectType::class, []],
    [KeyValueType::class, []],
    [NumberType::class, []],
    [TagsType::class, []],
    [TextAreaType::class, []],
    [TextType::class, []],

    [DownloadType::class, []],
    [FieldsetType::class, []],
    [GroupType::class, []],
    [ImageLayoutType::class, []],
    [SectionType::class, []],
    [SpaceType::class, []],
    [TextLayoutType::class, []],
    [TitleType::class, []],

    [RepeaterLayoutType::class, []],
];

$allOptionTypes = [
    ActionLabelTypeOption::class,
    AlpineMaskOption::class,
    BooleanOption::class,
    ColumnsOption::class,
    ColumnSpanOption::class,
    DateFormatOption::class,
    HelperTextTypeOption::class,
    IconOption::class,
    ImaginaryTypeOption::class,
    InLineLabelOption::class,
    InlineOption::class,
    MaxAmountOption::class,
    MaxLengthOption::class,
    MaxSelectOption::class,
    MaxValueOption::class,
    MinAmountOption::class,
    MinLengthOption::class,
    MinSelectOption::class,
    MinValueOption::class,
    NewLineOption::class,
    RelatedFieldOption::class,
    ReorderableTypeOption::class,
    RequiredOption::class,
    ShowAsFieldsetOption::class,
    ShowInViewOption::class,
    ShowLabelOption::class,
    ValidationAttributeOption::class,
    ValidationMessageOption::class
];

$prepareFunction = static function (string $optionClass, array $exclude = []) use ($allTypes) {
    $canUseTypes = [];
    foreach ($allTypes as $typeObj) {
        $typeClass = $typeObj[0];
        if (in_array($typeClass, $exclude, true)) {
            continue;
        }

        /**@var CustomFieldType $typeClass */
        $typeClass = $typeClass::make();
        $typeOptions = collect($typeClass->getFlattenExtraTypeOptions());
        foreach ($typeOptions as $typeOption) {
            if (!($typeOption instanceof $optionClass)) {
                continue;
            }
            $canUseTypes[] = $typeObj;
        }
    }

    return $canUseTypes;
};

test('name of the TypeOption settings component must be correct', function ($optionClass) {
    $name = 'NamedXyZ';
    $component = $optionClass::make()->getModifyOptionComponent($name);
    expect($component)
        ->toBeInstanceOf(Component::class)
        ->and($component->getStatePath(false))
        ->toBe($name);
})
    ->with($allOptionTypes);


describe('test default type options effect', function () use ($allTypes, $prepareFunction) {
    beforeEach(function () {
        $this->typeOptionTestBeforeEach();
    });

    afterEach(function () {
        $this->typeOptionTestAfterEach();
    });
    test('ColumnsOption test colum mutation', function ($customFieldTypeClass, $extraOptions) {
        /**@var CustomFieldType $customFieldType */
        $customOption = ColumnsOption::make();
        $customFieldType = $customFieldTypeClass::make();
        if (!$this->schuldTest($customOption, $customFieldType)) {
            expect(true)->toBeTrue();
            return;
        }

        $defaultColumns = $customFieldType->getFlattenExtraTypeOptions()['columns']->getDefaultValue() ?? 1;
        $columns = 3;

        $checkNoOptionFunction = function (Component $component) use ($defaultColumns) {
            expect($component)->not()->toBeNull()
                ->and($component->getColumns()['lg'])->toBe($defaultColumns);
        };

        $checkOptionFunction = function (Component $component) use ($columns) {
            expect($component)->not()->toBeNull()
                ->and($component->getColumns()['lg'])->toBe($columns);
        };

        $this->componentTestField(
            $customFieldType,
            $extraOptions,
            ['columns' => $columns],
            $checkNoOptionFunction,
            $checkOptionFunction
        );
    })
        ->with($allTypes);

    test('ColumnsOption test colum_span mutation', function ($customFieldTypeClass, array $extraOptions = []) {
        $customOption = ColumnsOption::make();
        $customFieldType = $customFieldTypeClass::make();
        if (!$this->schuldTest($customOption, $customFieldType)) {
            expect(true)->toBeTrue();
            return;
        }

        $defaultColumns = $customFieldType->getFlattenExtraTypeOptions()['column_span']->getDefaultValue() ?? 1;
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
            $customFieldType,
            $extraOptions,
            ['column_span' => $columnSpan],
            $checkNoOptionFunction,
            $checkOptionFunction
        );
    })
        ->with($allTypes);
});
