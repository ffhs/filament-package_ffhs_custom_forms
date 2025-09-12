<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\InfolistFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Components\Component;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\{Actions\Action, Component, Repeater};
use Filament\Infolists\Components\Component as InfolistComponent;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Collection;

class RepeaterLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;
    use CanRenderCustomForm;
    use CanLoadFormAnswer;

    public static function modifyRepeaterAction(Action $action): void
    {
        $oldAction = $action->getActionFunction();

        $action->action(function ($livewire, Repeater $component, Action $action) use ($oldAction) {
            $action->evaluate($oldAction);
            $livewire
                ->getForm('form')
                ->callAfterStateUpdated($component->getStatePath());
        });
    }

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
//      $ordered = $this->getOptionParameter($record, 'ordered');
        $minAmount = $this->getOptionParameter($record, 'min_amount');
        $maxAmount = $this->getOptionParameter($record, 'max_amount');
        $defaultAmount = $this->getOptionParameter($record, 'default_amount');
        $addActionLabel = $this->getOptionParameter($record, 'add_action_label');
        $columns = $this->getOptionParameter($record, 'columns');
        $columnStart = $this->getOptionParameter($record, 'new_line');

        $schema = $parameter['child_render']();

        /**@var Repeater $repeater */
        $repeater = $this->makeComponent(Repeater::class, $record, false, ['min_amount', 'max_amount']);
        $repeater
            ->columns($columns)
            ->columnStart($columnStart)
            ->defaultItems($defaultAmount)
            ->minItems($minAmount)
            ->maxItems($maxAmount)
            ->schema($schema)
            ->deleteAction(self::modifyRepeaterAction(...))
            ->addAction(self::modifyRepeaterAction(...))
            ->reorderable(false);

        if (!is_null($addActionLabel)) {
            $repeater->addActionLabel($addActionLabel);
        }

        return $repeater;
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistComponent {

        $label = $this->getLabelName($record);
        $isFieldset = $this->getOptionParameter($record, 'show_as_fieldset');
        $component = $isFieldset ? Fieldset::make($label) : Section::make($label);

        /** @var Collection $fields */
        $schema = [];

        $loadedAnswers = $this->loadCustomAnswerData(
            $record->customFormAnswer,
            $record->customField->custom_form_id,
            $record->customField->layout_end_position,
            $record->customField->customForm
        );
        $loadedAnswers = $loadedAnswers[$record->customField->identifier ?? ''] ?? [];

        $fields = $parameter['child_fields'];
        $fields = $fields->keyBy('form_position');
        $offset = $fields->sortBy('form_position')->first()->form_position - 1;
        $viewMode = $parameter['viewMode'];
        $customForm = $record->customFormAnswer->customForm;

        foreach ($loadedAnswers as $id => $answer) {
            $displayer = InfolistFieldDisplayer::make($record->customFormAnswer, $id);

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
}
