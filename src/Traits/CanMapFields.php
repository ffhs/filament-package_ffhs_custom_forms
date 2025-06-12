<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\CustomSplitType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Support\Collection;

trait CanMapFields
{
    public function getIdentifyKey(CustomField|CustomFieldAnswer $record): string
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        return $record->identifier;
    }

    public function getLabelName(CustomField|CustomFieldAnswer $record): string
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        $label = $record->name;
        $label = is_null($label) ? '' : $label;

        return str($label)->sanitizeHtml();
    }

    public function getOptionParameter(
        CustomField|CustomFieldAnswer $record,
        string $option,
        bool $canBeNull = false
    ): mixed {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        if (is_null($record->options)) {
            $record->options = [];
        }
        if (array_key_exists($option, $record->options)) {
            $return = $record->options[$option];
            if (!is_null($return)) {
                return $return;
            }

            if ($canBeNull) {
                return null;
            }
        }

        $generalOptions = $record->getType()->getDefaultGeneralOptionValues();
        if (array_key_exists($option, $generalOptions)) {
            return $generalOptions[$option];
        }
        $fieldOptions = $record->getType()->getDefaultTypeOptionValues();
        if (array_key_exists($option, $fieldOptions)) {
            return $fieldOptions[$option];
        }
        return $canBeNull ? null : 0;
    }

    public function hasOptionParameter(CustomField|CustomFieldAnswer $record, string $option): bool
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        if (!is_null($record->getType()->getFlattenExtraTypeOptions()[$option] ?? null)) {
            return true;
        }
        if (!is_null($record->getType()->getFlattenGeneralTypeOptions()[$option] ?? null)) {
            return true;
        }
        return false;
    }

    public function getAnswer(CustomFieldAnswer $answer)
    {
        $rawAnswerer = $answer->answer;
        if (is_null($rawAnswerer)) {
            return null;
        }
        return $answer->customField->getType()->prepareLoadAnswerData($answer, $rawAnswerer);
    }

    public function getAvailableCustomOptions(CustomField $record): Collection
    {
        return $record->customOptions->pluck('name', 'identifier');
    }

    public function getAllCustomOptions(CustomField|CustomFieldAnswer $record): Collection
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        if ($record->isInheritFromGeneralField()) {
            $options = $record->generalField->customOptions;
        } else {
            $options = $record->customOptions;
        }
        return $options->pluck('name', 'identifier');
    }

    public function getTypeConfigAttribute(CustomField|CustomFieldAnswer $record, string $attribute): mixed
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        return $record->getType()->getConfigAttribute($attribute);
    }

    /**
     * Like an Repeater
     *
     * @param CustomField|CustomFieldAnswer $field
     *
     * @return bool
     */
    public function isFieldInSplitGroup(CustomField|CustomFieldAnswer $record): bool
    {//ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        $fields = $record->customForm->customFields;
        $parentSplitField = $fields
            ->firstWhere(function (CustomField $field) use ($record) {
                if ($field->form_position >= $record->form_position) {
                    return false;
                }
                if ($field->layout_end_position < $record->form_position) {
                    return false;
                }
                return $field->getType() instanceof CustomSplitType;
            });
        return !is_null($parentSplitField);
    }

    public function getExistingPaths(
        CustomField|CustomFieldAnswer $record,
        CustomFormAnswer $customFormAnswer = null
    ): Collection { //ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $customFormAnswer = $record->customFormAnswer;
            $record = $record->customField;
        }

        $splitGroup = $this->getParentSplitGroups($record)
            ->sortByDesc('form_position')->first();

        $fieldsInGroup = $this->getFieldsInLayout($splitGroup);

        $answersWithPath = $customFormAnswer->customFieldAnswers
            ->whereIn('custom_field_id', $fieldsInGroup->pluck('id'))
            ->whereNotNull('path');

        return $answersWithPath->keyBy('path')->keys();
    }

    public function getParentSplitGroups(CustomField|CustomFieldAnswer $record): Collection
    {//ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        $fields = $record->customForm->customFields;
        return $fields
            ->where('form_position', '<', $record->form_position)
            ->where('layout_end_position', '>=', $record->form_position)
            ->filter(function (CustomField $field) use ($record) {
                return $field->getType() instanceof CustomSplitType;
            });
    }

    public function getFieldsInLayout(CustomField|CustomFieldAnswer $record): Collection
    { //ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }
        return $record->customForm->customFields
            ->filter(function (CustomField $field) use ($record) {
                if ($field->form_position > $record->layout_end_position) {
                    return false;
                }
                if ($field->form_position <= $record->form_position) {
                    return false;
                }
                return true;
            });
    }
}
