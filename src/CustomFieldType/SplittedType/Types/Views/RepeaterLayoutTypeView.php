<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\SplittedType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFieldDataContainer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
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
//      $ordered = $this->getOptionParameter($record, 'ordered'); ToDo add order for Repeater
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
        //ToDo Make for other non database fields
        $label = $this->getLabelName($customFieldAnswer);
        $isFieldset = $this->getOptionParameter($customFieldAnswer, 'show_as_fieldset');
        $component = $isFieldset ? Fieldset::make($label) : Section::make($label);

        $schema = [];

        $formAnswer = $customFieldAnswer->getCustomFormAnswer();
        $customField = $customFieldAnswer->getCustomField();
        $customFormAnswer = $formAnswer->getCustomForm();
        /**@phpstan-ignore-next-line */
        $customFormField = Customform::find($customField->custom_form_id);

        /**@phpstan-ignore-next-line */
        $relatedField = $customFormAnswer->id === $customFormField->id
            ? $customField
            : $customFormAnswer->getCustomFields()->firstWhere('template_id', $customFormField->id);

        /**@phpstan-ignore-next-line */
        $loadedAnswers = $this->loadCustomAnswerForEntry(
            $customFieldAnswer->getCustomFormAnswer(),
            $relatedField->getFormPosition(),
            $relatedField->getLayoutEndPosition() ?? $relatedField->getFormPosition()
        ) ?? [];

        $loadedAnswers = $loadedAnswers[$customField->identifier()] ?? [];

        /** @var Collection<int, CustomFieldDataContainer> $fields */ //ToDo make for the other non database fields
        $fields = $parameter['child_fields'];
        $fields = $fields->keyBy(fn(EmbedCustomField $container) => $container->getFormPosition());
        $viewMode = $parameter['viewMode'];

        foreach ($loadedAnswers as $id => $answer) {
            $displayer = EntryFieldDisplayer::make($customFieldAnswer->getCustomFormAnswer(), $id);

            $renderOutput = $this->renderCustomFormRaw(
                $viewMode,
                $displayer,
                $customFormField,
                $fields,
                $customField->getFormPosition()
            );
            [$subSchema, $allComponents] = $renderOutput;

            $parameter['registerComponents']($allComponents);

            $schema[] = Fieldset::make('')
                ->columnSpanFull()
                ->schema($subSchema)
                ->columns(1)
                ->statePath($id);
        }

        return $component
            ->columnSpanFull()
            ->schema($schema)
            ->columnStart(1)
            ->columns(1);
    }
}
