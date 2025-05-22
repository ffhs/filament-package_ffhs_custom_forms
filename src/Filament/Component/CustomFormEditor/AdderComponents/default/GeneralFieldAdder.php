<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\default;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Colors\Color;

final class GeneralFieldAdder extends FormEditorFieldAdder
{
    public function getGeneralFieldSelectOptions()
    {
        $formIdentifier = $this->getGetCallback()('custom_form_identifier');
        return once(static function () use ($formIdentifier) {
            $allowedGeneralFields = CustomForms::getAllowedGeneralFieldsInFormIdentifier($formIdentifier);

            //Mark Required GeneralFields
            return $allowedGeneralFields
                ->mapWithKeys(function (GeneralField $generalField) use ($formIdentifier) {
                    /**@var GeneralFieldForm $generalFieldForm */
                    $generalFieldForm = $generalField->generalFieldForms->firstWhere('custom_form_identifier',
                        $formIdentifier);

                    $name = ($generalFieldForm->is_required ? '* ' : '') . $generalField->name;

                    return [$generalField->id => $name];
                });
        });
    }

    public function isGeneralDisabled($value): bool
    {
        $notAllowed = once(function () {
            $fields = $this->getState()['custom_fields'];

            $usedGeneralFieldIds = collect($fields)
                ->pluck('general_field_id');

            $usedTemplates = collect($fields)
                ->pluck('template_id');

            $usedGeneralFieldIdsFormTemplates = CustomForm::query()
                ->whereIn('id', $usedTemplates)
                ->with('generalFields')
                ->select('id')
                ->get()
                ->pluck('generalFields')
                ->flatten(1)
                ->pluck('id');

            return $usedGeneralFieldIds->merge($usedGeneralFieldIdsFormTemplates);
        });

        return $notAllowed->contains($value);
    }

    protected function setUp(): void
    {
        $this->label(CustomForms::__('custom_form_editor.field_adder.general_field_adder.label'));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup('custom_fields')
                ->options($this->getGeneralFieldSelectOptions(...))
                ->disableOptionWhen($this->isGeneralDisabled(...))
                ->color(Color::Blue)
                ->action($this->getExpandAction(...))
        ]);
    }

    protected function getExpandAction($option): Action
    {
        return Action::make('addGeneral')
            ->action(function ($component, $arguments, $livewire) use ($option) {
                $generalField = GeneralField::cached($option);

                $field = [
                    'general_field_id' => $option,
                    'options' => $generalField->getType()->getDefaultTypeOptionValues(),
                    'is_active' => true,
                ];

                $this::addNewField($component, $arguments, $livewire, $field);
            });
    }
}
