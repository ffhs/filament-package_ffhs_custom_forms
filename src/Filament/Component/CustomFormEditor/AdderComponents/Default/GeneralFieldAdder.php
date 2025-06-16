<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

final class GeneralFieldAdder extends FormEditorFieldAdder
{

    public function getCustomFormConfiguration(): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($this->getGetCallback()('custom_form_identifier'));
    }

    public function getGeneralFieldSelectOptions()
    {
        return once(function () {
            $formIdentifier = $this->getGetCallback()('custom_form_identifier');
            $customFormConfiguration = $this->getCustomFormConfiguration();

            return $customFormConfiguration->getAvailableGeneralFields()
                ->mapWithKeys(function (GeneralField $generalField) use ($formIdentifier) {
                    //Mark Required GeneralFields
                    /**@var GeneralFieldForm $generalFieldForm */
                    $generalFieldForm = $generalField->generalFieldForms
                        ->firstWhere('custom_form_identifier', $formIdentifier);
                    $name = ($generalFieldForm->is_required ? '* ' : '') . $generalField->name;
                    return [$generalField->id => $name];
                });
        });
    }

    public function isGeneralDisabled($value, CustomForm $record): bool
    {
        $notAllowed = once(function () use ($record) {
            $fields = $this->getState()['custom_fields'];

            $usedGeneralFieldIds = collect($fields)
                ->pluck('general_field_id');

            if ($record->isTemplate()) {
                return $usedGeneralFieldIds;
            }

            $usedTemplates = collect($fields)
                ->pluck('template_id');

            $usedTemplates = $record
                ->getFormConfiguration()
                ->getAvailableTemplates()
                ->whereIn('id', $usedTemplates);

            $usedGeneralFieldIdsFormTemplates = $usedTemplate
                ->map(fn(CustomForm $template) => $template->ownedFields)
                ->flatten(1)
                ->whereNotNull('general_field_id')
                ->pluck('general_field_id');

            return $usedGeneralFieldIds->merge($usedGeneralFieldIdsFormTemplates);
        });

        return $notAllowed->contains($value);
    }

    protected function setUp(): void
    {
        $this->label(CustomForms::__('custom_form_editor.field_adder.general_field_adder.label'));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup(fn(Get $get) => 'custom_fields-' . $get('custom_form_identifier'))
                ->options($this->getGeneralFieldSelectOptions(...))
                ->disableOptionWhen($this->isGeneralDisabled(...))
                ->color(Color::Blue)
                ->action($this->getExpandAction(...))
        ]);
    }

    protected function getExpandAction($option): Action
    {
        return Action::make('addGeneral')
            ->action(function ($component, $arguments, $livewire, CustomForm $record) use ($option) {
                $generalField = $record->getFormConfiguration()
                    ->getAvailableGeneralFields()
                    ->firstWhere('id', $option);

                $field = [
                    'general_field_id' => $option,
                    'options' => $generalField->getType()->getDefaultTypeOptionValues(),
                    'is_active' => true,
                ];

                $this::addNewField($component, $arguments, $livewire, $field);
            });
    }
}
