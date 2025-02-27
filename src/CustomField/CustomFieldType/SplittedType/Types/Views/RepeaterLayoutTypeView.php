<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\{Actions\Action, Component, Repeater};
use Filament\Infolists\Components\Component as InfolistComponent;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Collection;

class RepeaterLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        $ordered = FieldMapper::getOptionParameter($record, 'ordered');
        $minAmount = FieldMapper::getOptionParameter($record, 'min_amount');
        $maxAmount = FieldMapper::getOptionParameter($record, 'max_amount');
        $defaultAmount = FieldMapper::getOptionParameter($record, 'default_amount');
        $addActionLabel = FieldMapper::getOptionParameter($record, 'add_action_label');

        $schema = $parameter['renderer']();

        /**@var Repeater $repeater */
        $repeater = static::makeComponent(Repeater::class, $record, ['min_amount', 'max_amount']);
        $repeater
//            ->columns(FieldMapper::getOptionParameter($record, 'columns'))
//            ->columnStart(FieldMapper::getOptionParameter($record, 'new_line'))
            ->defaultItems($defaultAmount)
            ->minItems($minAmount)
            ->maxItems($maxAmount)
            ->schema($schema)
            ->deleteAction(self::modifyRepeaterAction(...))
            ->addAction(self::modifyRepeaterAction(...));

        if (!is_null($addActionLabel)) {
            $repeater->addActionLabel($addActionLabel);
        }

        if ($ordered) {
            $repeater->orderColumn('order');
        } else {
            $repeater->reorderable(false);
        }

        return $repeater;
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistComponent {
//        $ordered = FieldMapper::getOptionParameter($record,'ordered');

        $isFieldset = FieldMapper::getOptionParameter($record, 'show_as_fieldset');
        $component = $isFieldset
            ? Fieldset::make('')//FieldMapper::getLabelName($record)
            : Section::make(FieldMapper::getLabelName($record));

        /** @var Collection $fields */
        $schema = [];

        $loadedAnswers = CustomFormLoadHelper::load(
            $record->customFormAnswer,
            $record->customField->form_position,
            $record->customField->layout_end_position,
            $record->customForm
        );
        $loadedAnswers = $loadedAnswers[$record->customField->identifier ?? ''] ?? [];

        $fields = $parameter['customFieldData'];
        $fields = $fields->keyBy('form_position');
        $offset = $fields->sortBy('form_position')->first()->form_position - 1;
        $viewMode = $parameter['viewMode'];
        $customForm = $record->customFormAnswer->customForm;

        $answerersFields = $record
            ->customFormAnswer
            ->customFieldAnswers
            ->whereIn('custom_field_id', $fields->pluck('id'));

        foreach ($loadedAnswers as $id => $answer) {
            $render = CustomFormRender::getInfolistRender(
                $parameter['viewMode'],
                $customForm,
                $record->customFormAnswer,
                $answerersFields,
                $id
            );

            $renderOutput = CustomFormRender::renderRaw($offset, $fields, $render, $viewMode, $customForm);
            $subSchema = $renderOutput[0];
            $allComponents = $renderOutput[1];

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
}
