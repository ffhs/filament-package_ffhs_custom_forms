<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\CustomSplitType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Illuminate\Support\Collection;

trait CanMapFields
{
    public function getIdentifyKey(EmbedCustomField|EmbedCustomFieldAnswer $record): string
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        return $record->identifier;
    }

    public function getLabelName(EmbedCustomField|EmbedCustomFieldAnswer $record): string
    {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
        }

        $label = $record->name;
        $label = is_null($label) ? '' : $label;

        return str($label)->sanitizeHtml();
    }

    public function getOptionParameter(
        EmbedCustomField|EmbedCustomFieldAnswer $record,
        string $option,
        bool $canBeNull = false
    ): mixed {
        if ($record instanceof EmbedCustomFieldAnswer) {
            $record = $record->getCustomField();
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

        $generalOptions = $record
            ->getType()
            ->getDefaultGeneralOptionValues();

        if (array_key_exists($option, $generalOptions)) {
            return $generalOptions[$option];
        }

        $fieldOptions = $record
            ->getType()
            ->getDefaultTypeOptionValues();

        if (array_key_exists($option, $fieldOptions)) {
            return $fieldOptions[$option];
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
        $rawAnswerer = $answer->answer;

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
        return $record
            ->customOptions //ToDo fix
            ->pluck('name', 'identifier');
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

    public function getTypeConfigAttribute(EmbedCustomField|CustomFieldAnswer $record, string $attribute): mixed
    {
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }

        return $record
            ->getType()
            ->getConfigAttribute($attribute);
    }

    /**
     * Like an Repeater
     */
    public function isFieldInSplitGroup(EmbedCustomField|CustomFieldAnswer $record): bool
    {//ToDo Slow
        if ($record instanceof CustomFieldAnswer) {
            $record = $record->customField;
        }

        $fields = $record
            ->customForm
            ->customFields;
        $parentSplitField = $fields
            ->firstWhere(function (CustomField $field) use ($record) {
                if ($field->form_position >= $record->form_position
                    || $field->layout_end_position < $record->form_position) {
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

        $fields = $record
            ->customForm
            ->customFields;

        return $fields
            ->where('form_position', '<', $record->form_position)
            ->where('layout_end_position', '>=', $record->form_position)
            ->filter(fn(CustomField $field) => $field->getType() instanceof CustomSplitType);
    }

//    public function getFieldsInLayout(EmbedCustomField|CustomFieldAnswer $record): Collection //ToDo Check if its needed
//    {
//        if ($record instanceof CustomFieldAnswer) {
//            $record = $record->customField;
//        }
//
//        return $record
//            ->customForm
//            ->customFields
//            ->filter(function (CustomField $field) use ($record) {
//                return !($field->form_position > $record->layout_end_position
//                    || $field->form_position <= $record->form_position);
//            });
//    }
//
//    public function getExistingPaths(
//        EmbedCustomField|CustomFieldAnswer $record,
//        CustomFormAnswer $customFormAnswer
//    ): Collection {
//        if ($record instanceof CustomFieldAnswer) {
//            $customFormAnswer = $record->customFormAnswer;
//            $record = $record->customField;
//        }
//
//        $splitGroup = $this
//            ->getParentSplitGroups($record)
//            ->sortByDesc('form_position')
//            ->first();
//
//        $fieldsInGroup = $this->getFieldsInLayout($splitGroup);
//
//        $answersWithPath = $customFormAnswer
//            ->customFieldAnswers
//            ->whereIn('custom_field_id', $fieldsInGroup->pluck('id'))
//            ->whereNotNull('path');
//
//        return $answersWithPath
//            ->keyBy('path')
//            ->keys();
//    }

}
