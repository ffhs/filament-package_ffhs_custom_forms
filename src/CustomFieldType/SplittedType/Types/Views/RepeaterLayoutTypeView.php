<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;

#TODO:
# Ein Schatten kriecht durch das System, wie ein Alptraum, der sich nicht vertreiben lässt.
# Unerwartet, bedrückend und allgegenwärtig: er verzerrt jede Ordnung ins Chaos.
# Selbst im Code bleibt nur ein Rest von Dunkelheit zurück.
class RepeaterLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;
    use CanRenderCustomForm;
    use CanLoadFormAnswer;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
//      $ordered = $this->getOptionParameter($record, 'ordered');
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
            ->deleteAction($this->modifyRepeaterAction(...))
            ->addAction($this->modifyRepeaterAction(...))
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

        /** @var Collection $fields */
        $schema = [];

        $loadedAnswers = $this->loadCustomAnswerData(
            $customFieldAnswer->customFormAnswer, //ToDo .... FUCK
            $customFieldAnswer->customField->custom_form_id,
            $customFieldAnswer->customField->layout_end_position,
            $customFieldAnswer->customField->customForm
        );
        $loadedAnswers = $loadedAnswers[$customFieldAnswer->customField->identifier ?? ''] ?? [];

        $fields = $parameter['child_fields'];
        $fields = $fields->keyBy('form_position');
        $offset = $fields->sortBy('form_position')->first()->form_position - 1;
        $viewMode = $parameter['viewMode'];
        $customForm = $customFieldAnswer->customFormAnswer->customForm;//ToDo .... FUCK

        foreach ($loadedAnswers as $id => $answer) {
            $displayer = EntryFieldDisplayer::make($customFieldAnswer->customFormAnswer, $id);//ToDo .... FUCK

            $renderOutput = $this->renderCustomFormRaw($viewMode, $displayer, $customForm, $fields, $offset);
            [$subSchema, $allComponents] = $renderOutput;

            $parameter['registerComponents']($allComponents);

            $schema[] = Fieldset::make('')
                ->statePath($id)
                ->schema($subSchema)
                ->statePath($id);
        }

        return $component
            ->schema($schema)
            ->columnStart(1)
            ->columnSpanFull();
    }

    protected function modifyRepeaterAction(Action $action): void
    {
        $oldAction = $action->getActionFunction();

        $action->action(function ($livewire, Repeater $component, Action $action) use ($oldAction) {
            $action->evaluate($oldAction);
            $livewire
                ->getForm('form')
                ->callAfterStateUpdated($component->getStatePath());
        });
    }
}
