<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\PrioritizeSelect;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView {
        HasCustomOptionInfoListView::getInfolistComponent as getInfolistComponentNormalSelect;
    }
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $several = $this->getOptionParameter($record, 'several');
        $prioritized = $this->getOptionParameter($record, 'prioritized');

        if ($several && $prioritized) {
            return $this->getPrioritizedSelect($record, $parameter);
        }

        return $this->getSingleSelect($record);
    }

    public function getPrioritizedSelect(
        CustomField $record,
        array $parameter
    ): Component|\Filament\Infolists\Components\Component {
        /**@var PrioritizeSelect $select */
        $select = $this->makeComponent(PrioritizeSelect::class, $record);

        $selectLabelTranslation = __('filament-package_ffhs_custom_forms::custom_forms.fields.type_view.select.select');
        $labels = $this->getOptionParameter($record, 'prioritized_labels');
        $labels = array_values($labels);
        $validationMessagesRaw = $this->getOptionParameter($record, 'validation_messages_prioritized');

        $validationMessages = [];
        foreach ($validationMessagesRaw as $message) {
            $validationMessages[$message['select_id'] ?? ''][$message['rule'] ?? ''] = $message['message'] ?? '';
        }


        return $select
            ->minItems($this->getOptionParameter($record, 'min_select'))
            ->maxItems($this->getOptionParameter($record, 'max_select'))
            ->options($this->getAvailableCustomOptions($record))
            ->dynamic($this->getOptionParameter($record, 'dynamic_prioritized'))
            ->mutateSelectUsing(
                function (Select $select, $selectId) use ($validationMessages, $labels, $selectLabelTranslation) {
                    $label = $selectId + 1 . '. ' . $selectLabelTranslation;
                    if (array_key_exists($selectId, $labels)) {
                        $label = $labels[$selectId]['label'] ?? '';
                    }


                    return $select
                        ->validationMessages($validationMessages[$selectId] ?? [])
                        ->label($label);
                }
            );
    }

    public function getSingleSelect(CustomField $record): Select
    {
        /** @var Select $select */
        $select = $this->makeComponent(Select::class, $record);
        $select = $select->options($this->getAvailableCustomOptions($record));
        $required = $this->getOptionParameter($record, 'required');
        $minItems = $required ? $this->getOptionParameter($record, 'min_select') : 0;

        if ($this->getOptionParameter($record, 'several')) {
            $maxItems = $this->getOptionParameter($record, 'max_select');
            $select = $select->multiple()->minItems($minItems);
            if ($maxItems > 0) {
                $select->maxItems($maxItems);
            }
        }
        return $select;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): \Filament\Infolists\Components\Component {
        $several = $this->getOptionParameter($record, 'several');
        $prioritized = $this->getOptionParameter($record, 'prioritized');

        if (!($several && $prioritized)) {
            return $this->getInfolistComponentNormalSelect($type, $record, $parameter);
        }

        /**@var TextEntry $textEntry */
        $textEntry = $this->makeComponent(TextEntry::class, $record);
        $answer = $this->getAnswer($record) ?? [];
        $stateList = $this->getAllCustomOptions($record)
            ->filter(fn($value, $id) => in_array($id, $answer, false));

        $cleanedAnswers = [];
        if (!is_array($answer)) {
            $answer = [];
        }
        foreach ($answer as $key => $value) {
            if (is_null($value) || !str_contains($key, 'prioritized_')) {
                continue;
            }
            $selectId = str_replace('prioritized_', '', $key);

            $name = $stateList->toArray()[$value] ?? '';
            $translatedSelect = $type->getTranslatedName();
            $cleanedAnswers[$selectId] = $selectId + 1 . '. ' . $translatedSelect . ': ' . $name;
        }

        ksort($cleanedAnswers, SORT_NUMERIC);

        return $textEntry
            ->state($cleanedAnswers)
            ->listWithLineBreaks()
            ->columnSpanFull()
            ->inlineLabel()
            ->badge();
    }
}
