<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;

class RepeaterLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;
    use CanRenderCustomForm;
    use CanLoadFormAnswer;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
//      $ordered = $this->getOptionParameter($record, 'ordered'); ToDo
        $minAmount = $this->getOptionParameter($customField, 'min_amount');
        $maxAmount = $this->getOptionParameter($customField, 'max_amount');
        $defaultAmount = $this->getOptionParameter($customField, 'default_amount');
        $addActionLabel = $this->getOptionParameter($customField, 'add_action_label');
        $columns = $this->getOptionParameter($customField, 'columns');
        $columnStart = $this->getOptionParameter($customField, 'new_line');

        $schema = $parameter['child_render']();

        /**@var Repeater $repeater */
        $repeater = $this->makeComponent(Repeater::class, $customField, false, ['min_amount', 'max_amount']);
        $repeater
            ->columns($columns)
            ->columnStart($columnStart)
            ->defaultItems($defaultAmount)
            ->minItems($minAmount)
            ->maxItems($maxAmount)
            ->schema($schema)
            ->reorderable(false); //ToDo add order

        if (!is_null($addActionLabel)) {
            $repeater->addActionLabel($addActionLabel);
        }

        return $repeater;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {

        $label = $this->getLabelName($customFieldAnswer);
        $isFieldset = $this->getOptionParameter($customFieldAnswer, 'show_as_fieldset');
        $component = $isFieldset ? Fieldset::make($label) : Section::make($label);

        $schema = [];

        $customField = $customFieldAnswer->getCustomField();

        $loadedAnswers = $this->loadCustomAnswerForEntry(
            $customFieldAnswer->getCustomFormAnswer(),
            $customField->getFormPosition(),
            $customField->getLayoutEndPosition(),
        );

        $loadedAnswers = $loadedAnswers[$customFieldAnswer->getCustomField()->identifier ?? ''] ?? [];

        /** @var Collection $fields */
        $fields = $parameter['child_fields'];
        $fields = $fields->keyBy('form_position');
        $offset = $fields->sortBy('form_position')->first()->form_position - 1;
        $viewMode = $parameter['viewMode'];
        $customForm = $customFieldAnswer->getCustomForm();

        foreach ($loadedAnswers as $id => $answer) {
            $displayer = EntryFieldDisplayer::make($customFieldAnswer->getCustomFormAnswer(), $id);

            $renderOutput = $this->renderCustomFormRaw($viewMode, $displayer, $customForm, $fields, $offset);
            [$subSchema, $allComponents] = $renderOutput;

            $parameter['registerComponents']($allComponents);

            $schema[] = Fieldset::make('')
                ->schema($subSchema)
                ->statePath($id);
        }

        return $component
            ->columnSpanFull()
            ->schema($schema)
            ->columnStart(1);
    }
}
