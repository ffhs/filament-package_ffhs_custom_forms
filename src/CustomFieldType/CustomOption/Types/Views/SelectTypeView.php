<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FfhsUtils\Filament\Component\PrioritizeSelect;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;

class SelectTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListView {
        HasCustomOptionInfoListView::getEntryComponent as getEntryComponentNormalSelect;
    }
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $several = $this->getOptionParameter($customField, 'several');
        $prioritized = $this->getOptionParameter($customField, 'prioritized');

        if ($several && $prioritized) {
            return $this->getPrioritizedSelect($customField, $parameter);
        }

        return $this->getSingleSelect($customField);
    }

    public function getPrioritizedSelect(EmbedCustomField $customField, array $parameter): Component
    {
        $select = $this->makeComponent(PrioritizeSelect::class, $customField, false);

        $selectLabelTranslation = __('filament-package_ffhs_custom_forms::custom_forms.fields.type_view.select.select');
        $labels = $this->getOptionParameter($customField, 'prioritized_labels');
        $labels = array_values($labels);
        $validationMessagesRaw = $this->getOptionParameter($customField, 'validation_messages_prioritized');

        $validationMessages = [];
        foreach ($validationMessagesRaw as $message) {
            $validationMessages[$message['select_id'] ?? ''][$message['rule'] ?? ''] = $message['message'] ?? '';
        }

        return $select
            ->minItems($this->getOptionParameter($customField, 'min_select'))
            ->maxItems($this->getOptionParameter($customField, 'max_select'))
            ->options($this->getAvailableCustomOptions($customField))
            ->dynamic($this->getOptionParameter($customField, 'dynamic_prioritized'))
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

    public function getSingleSelect(EmbedCustomField $customField): Select
    {
        /** @var Select $select */
        $select = $this->makeComponent(Select::class, $customField, false);
        $select = $select->options($this->getAvailableCustomOptions($customField));
        $required = $this->getOptionParameter($customField, 'required');
        $minItems = $required ? $this->getOptionParameter($customField, 'min_select') : 0;

        if ($this->getOptionParameter($customField, 'several')) {
            $maxItems = $this->getOptionParameter($customField, 'max_select');
            $select = $select
                ->multiple()
                ->minItems($minItems);

            if ($maxItems > 0) {
                $select->maxItems($maxItems);
            }
        }

        return $select;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $several = $this->getOptionParameter($customFieldAnswer, 'several');
        $prioritized = $this->getOptionParameter($customFieldAnswer, 'prioritized');

        if (!($several && $prioritized)) {
            return $this->getEntryComponentNormalSelect($customFieldAnswer, $parameter);
        }

        /**@var TextEntry $textEntry */
        $textEntry = $this->makeComponent(TextEntry::class, $customFieldAnswer, true);
        $answer = $this->getAnswer($customFieldAnswer) ?? [];
        $stateList = $this
            ->getAllCustomOptions($customFieldAnswer)
            ->filter(fn($value, $id) => in_array($id, $answer, false));

        if (!is_array($answer)) {
            $answer = [];
        }

        $cleanedAnswers = [];
        foreach ($answer as $key => $value) {
            if (is_null($value) || !str_contains($key, 'prioritized_')) {
                continue;
            }

            $selectId = str_replace('prioritized_', '', $key);

            $name = $stateList->toArray()[$value] ?? '';
            $translatedSelect = $customFieldAnswer->getType()->displayname();
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
