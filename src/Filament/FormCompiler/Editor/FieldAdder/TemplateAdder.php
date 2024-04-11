<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class TemplateAdder extends CustomFieldAdder
{

    function getTitle(): string {
        if($this->form->is_template) return "";
        return  "Templates"; //ToDo Translate
    }

    function getSchema(): array {

        if($this->form->is_template) return [];

        $templateOptions =  CustomForm::getTemplateTypesToAdd($this->form->getFormConfiguration())
            ->pluck("short_title", "id");

        return [
            Select::make("add_template_id")
                ->disableOptionWhen(fn($value, Get $get)=>  $this->isTemplateDisabled($value,$get))
                ->options($templateOptions)
                ->native(false)
                ->label("")
                ->live(),

            Actions::make([
                Action::make("add_template")
                    ->closeModalByClickingAway(false)
                    ->label(fn() => "Hinzufügen ") //ToDo Translate
                    ->disabled(function(Get $get){
                        $templateID = $get("add_template_id");
                        if(is_null($templateID)) return true;
                        return $this->isTemplateDisabled($templateID,$get);
                    })
                    ->action(function ($set, Get $get) {
                        $data=["template_id" => $get("add_template_id")];
                        CustomFormEditorHelper::setCustomFieldInRepeater($data, $get, $set);
                        $set("add_template_id", null);
                    }),
            ]),
        ];
    }


    private function isTemplateDisabled($value, Get $get): bool {
        if($this->useTemplateUsedGeneralFields($value,$get)) return true;
        $customFields = $get("custom_fields");
        $templates = CustomFormEditorHelper::getFieldsWithProperty($customFields,"template_id");
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);
        return in_array($value,$usedTemplateIds);
    }

    public function useTemplateUsedGeneralFields(int $templateId, Get $get): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = CustomFormEditorHelper::getUsedGeneralFieldIds($get("custom_fields"));
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }
}
