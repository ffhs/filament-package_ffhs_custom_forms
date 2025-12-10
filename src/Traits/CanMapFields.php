<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\CustomSplitType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Support\Collection;

trait CanMapFields
{
    public function getIdentifyKey(EmbedCustomField|EmbedCustomFieldAnswer $record): string
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        return $record->identifier();
    }

    public function getLabelName(EmbedCustomField|EmbedCustomFieldAnswer $record): string
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        return str($record->getName() ?? '')->sanitizeHtml();
    }

    public function getOptionParameter(
        EmbedCustomField|EmbedCustomFieldAnswer $record,
        string $option,
        bool $canBeNull = false
    ): mixed {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        // Check record-specific options first
        $options = $record->getOptions();
        if (array_key_exists($option, $options) && (!is_null($options[$option]) || $canBeNull)) {
            return $options[$option];
        }

        // Fall back to type defaults
        $type = $record->getType();
        $generalOptions = $type->getDefaultGeneralOptionValues();

        if (array_key_exists($option, $generalOptions)) {
            return $generalOptions[$option];
        }

        $fieldOptions = $type->getDefaultTypeOptionValues();

        if (array_key_exists($option, $fieldOptions)) {
            return $fieldOptions[$option];
        }

        return $canBeNull ? null : 0;
    }

    public function getOptionParameterWithCached(
        string $option,
        bool $canBeNull,
        array $defaultValues,
        array $generalDefaultValues,
        array $options
    ): mixed {

        // Check record-specific options first
        if (array_key_exists($option, $options) && (!is_null($options[$option]) || $canBeNull)) {
            return $options[$option];
        }

        if (array_key_exists($option, $generalDefaultValues)) {
            return $generalDefaultValues[$option];
        }

        if (array_key_exists($option, $defaultValues)) {
            return $defaultValues[$option];
        }

        return $canBeNull ? null : 0;
    }

    public function hasOptionParameter(EmbedCustomField|CustomFieldAnswer $record, string $option): bool
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->getCustomField();;
        }

        return !is_null($record->getType()->getFlattenExtraTypeOptions()[$option] ?? null)
            || !is_null($record->getType()->getFlattenGeneralTypeOptions()[$option] ?? null);
    }

    public function getAnswer(EmbedCustomFieldAnswer $answer)
    {
        $rawAnswerer = $answer->getAnswer();

        if (is_null($rawAnswerer)) {
            return null;
        }

        return $answer
            ->getCustomField()
            ->getType()
            ->prepareLoadAnswerData($answer, $rawAnswerer);
    }

    public function getAvailableCustomOptions(EmbedCustomField $record): Collection
    {

        if ($record->isGeneralField()) {
            $options = $record->getGeneralField()->customOptions;
        } else {
            $options = $record->getCustomOptions();
        }

        return $options
            ->mapWithKeys(function (mixed $option) {
                if (!is_array($option)) {
                    return [$option->identifier => $option->name];
                }
                $name = $option['name'];
                if (is_array($name)) {
                    $name = $name[app()->getLocale()] ?? $name[app()->getFallbackLocale()] ?? '';
                }
                return [$option['identifier'] => $name];
            });
    }

    public function getAllCustomOptions(EmbedCustomField|EmbedCustomFieldAnswer $record): Collection
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        if ($record->isGeneralField()) {
            $options = $record
                ->getGeneralField()
                ->customOptions;
        } else {
            $options = $record->getCustomOptions();
        }

        return $options->pluck('name', 'identifier');
    }

    public function getTypeConfigAttribute(EmbedCustomField|EmbedCustomFieldAnswer $record, string $attribute): mixed
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        return $record->getType()
            ->getConfigAttribute($attribute, $record->getFormConfiguration());
    }

    /**
     * Like an Repeater
     */
    public function isFieldInSplitGroup(EmbedCustomField|EmbedCustomFieldAnswer $record): bool
    {//ToDo Slow
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        /**@phpstan-ignore-next-line */ //ToDo make for non CustomForm
        $fields = $record->customForm->getCustomFields();
        $parentSplitField = $fields
            ->firstWhere(function (CustomField $field) use ($record) {
                if ($field->getFormPosition() >= $record->getFormPosition()
                    || $field->getLayoutEndPosition() < $record->getFormPosition()) {
                    return false;
                }

                return $field->getType() instanceof CustomSplitType;
            });

        return !is_null($parentSplitField);
    }

    public function getParentSplitGroups(EmbedCustomField|CustomFieldAnswer $record): Collection
    {//ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }

        /**@phpstan-ignore-next-line */ //ToDo make for non CustomForm
        $fields = $record->customForm->customFields;

        return $fields
            ->where('form_position', '<', $record->getFormPosition())
            ->where('layout_end_position', '>=', $record->getFormPosition())
            ->filter(fn(CustomField $field) => $field->getType() instanceof CustomSplitType);
    }

    public function getFieldsInLayout(EmbedCustomField|CustomFieldAnswer $record): Collection
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->getCustomField();;
        }

        /**@phpstan-ignore-next-line */ //ToDo make for non CustomForm
        return $record->customForm->getCustomFields()
            ->filter(function (CustomField $field) use ($record) {
                return !($field->getFormPosition() > $record->getLayoutEndPosition()
                    || $field->getFormPosition() <= $record->getFormPosition());
            });
    }

    public function getExistingPaths(
        CustomField|CustomFieldAnswer $record,
        CustomFormAnswer $customFormAnswer
    ): Collection {
        if ($record instanceof CustomFieldAnswer) {
            $customFormAnswer = $record->customFormAnswer;
            $record = $record->getCustomField();
        }

        $splitGroup = $this
            ->getParentSplitGroups($record)
            ->sortByDesc('form_position')
            ->first();

        $fieldsInGroup = $this->getFieldsInLayout($splitGroup);

        $answersWithPath = $customFormAnswer
            ->customFieldAnswers
            ->whereIn('custom_field_id', $fieldsInGroup->pluck('id'))
            ->whereNotNull('path');

        return $answersWithPath
            ->keyBy('path')
            ->keys();
    }

}
