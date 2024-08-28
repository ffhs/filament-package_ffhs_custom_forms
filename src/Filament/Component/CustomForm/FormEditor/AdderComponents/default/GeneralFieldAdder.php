<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Cache;

final class GeneralFieldAdder extends FormEditorFieldAdder
{
    protected function setUp(): void
    {
        $this->label(__("filament-package_ffhs_custom_forms::custom_forms.form.compiler.general_fields"));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup("custom_fields")
                ->options($this->getGeneralFieldSelectOptions(...))
                ->disableOptionWhen($this->isGeneralDisabled(...))
                ->color(Color::Blue)
                ->action(fn($option)=>
                     Action::make("addGeneral")->action(fn($component,$arguments) => $this->createField($arguments, $component, $option))
                )
        ]);
    }

    public function createField(array $arguments, $component, $generalFieldId): void
    {
        $generalField = GeneralField::cached($generalFieldId);

        $field = [
            "general_field_id" => $generalFieldId,
            "options" => $generalField->getType()->getDefaultTypeOptionValues(),
            "is_active" => true,
        ];

        $this::addNewField($component, $arguments, $field);
    }

    public function getGeneralFieldSelectOptions() {
        $formIdentifier = data_get($this->getState(), "custom_form_identifier");
        return Cache::remember($formIdentifier . '_general_fields_allowed_in_form', GeneralField::getCacheDuration(), function () use ($formIdentifier) {

            //ToDo do minimize Models and remove GeneralFieldForm with join
            $generalFieldForms = GeneralFieldForm::query()->where("custom_form_identifier", $formIdentifier);
            $generalFields = GeneralField::query()->whereIn("id", $generalFieldForms->clone()->select("id"))->get();

            GeneralField::addToModelCache($generalFields);

            //Mark Required GeneralFields
            $generalFields = $generalFieldForms->get()->map(function (GeneralFieldForm $generalFieldForm) {
                $generalField = $generalFieldForm->generalField;

                if ($generalFieldForm->is_required) {
                    $generalField->name = "* ".$generalField->name;
                    $generalField->name = "* ".$generalField->name;
                }
                return $generalField;
            });

            return $generalFields->pluck("name", "id");
        });





    }


    public function isGeneralDisabled($value):bool {

        $notAllowed =   Cache::remember($this->getState()['id'] . '_general_fields_not_allowed_in_form', GeneralField::getCacheDuration()/4.0, function (){
            $fields = $this->getState()['custom_fields'];

            //ToDo  Improve
            $usedGeneralFieldIds = collect($fields)->whereNotNull("general_field_id")->pluck("general_field_id");
            $usedGeneralFieldIdsFormTemplates = collect($fields)
                ->whereNotNull("template_id")
                ->map(fn($templateField) => CustomForm::cached($templateField['template_id'])->generalFields)->flatten(1)->pluck("id");

            return $usedGeneralFieldIds->merge($usedGeneralFieldIdsFormTemplates);
        });

        return $notAllowed->contains($value) ;

    }

}
