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

#TODO:

# Logbuch Eintrag 19.09.2025
# Unter mir ruht ein s̴c̶h̷l̶a̷f̵e̴n̵d̴e̶s̴ Ungetüm, ein F͟r͟a͟g͟m͟e͟n͟t aus Dunkelheit, das jeden H̶̻͗e̴̡̺͈͐̓l̸͕͂d̸̡͓̊e̴̛̘̼͘n̵͙͗ zerbricht, sobald es… ȇ̶͖͙͎̐̕r̷̡͓͑̕w̶̘͊a̶̙̍͌c̴̨̝͋h̸̝͖̊͝t.
# Es ist das verzerrte Resultat falscher Entscheidungen, e͟i͟n͟ F͟e͟h͟l͟e͟r, der niemals hätte existieren dürfen.
# Es ernährt sich nicht von Fleisch, nicht von Blut—s̶̝̮͍͒͌o̷͉͕͘n̶̰̒͘d̷̪͍͗͘e̵͖͝ṟ̷͕̄n̴̺̎ von den Seelen derer, die zu tief schauen.
# Ich höre das Kratzen in meinen Gedanken, w̴h̶e̴r̶e̷ ̵m̷y̸ ̴o̸w̴n̷ ̶v̶o̶i̵c̶e̴ ̷d̵i̵s̷t̷o̴r̵t̵s̴.
# W̴̡̗̑̔e̴͕͂r̴̛̥͖̄ ̷̫̓w̶̰̤̋i̶̼͛r̸̝͆d̶̘͌ ̸̤̜͆ḋ̷͈i̵̺͛̕e̶̢̾s̵͎̋e̵͖̓m̴̲̞͘ Ungetüm entgegentreten? Wer wagt den Blick in die Tiefe, wissend,
# dass dort unten kein Leben schläft, sondern ein E̷̘̝͌͐͝n̵̼̯͋͝d̸͓̄̑e̵̝͝—und etwas, das schlimmer ist als jedes Ende
#
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

        $customField = $customFieldAnswer->getCustomField();

        $loadedAnswers = $this->loadCustomAnswerForEntry(
            $customFieldAnswer->getCustomFormAnswer(),
            $customField->form_position,
            $customField->layout_end_position,
        );

        //ToDo the problem is thath the shit doesnt load the answers ffor the sup stufff -_-

        $loadedAnswers = $loadedAnswers[$customFieldAnswer->getCustomField()->identifier ?? ''] ?? [];

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
