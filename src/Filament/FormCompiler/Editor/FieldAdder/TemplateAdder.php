<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

final class TemplateAdder extends FormEditorFieldAdder
{

    function getTitle(): string {
        if($this->form->is_template) return "";
        return  __("filament-package_ffhs_custom_forms::custom_forms.navigation.templates");
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
                    ->label(fn() => __("filament-package_ffhs_custom_forms::custom_forms.functions.add"))
                    ->requiresConfirmation(fn($get) => $this->hasExistingFields($get("custom_fields"),$get("add_template_id")))
                    ->modalHeading(function ($get){
                        if(!$this->hasExistingFields($get("custom_fields"), $get("add_template_id"))) return "";
                        return __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.template_has_existing_fields");
                    })
                    ->modalDescription(function ($get){
                        if(!$this->hasExistingFields($get("custom_fields"), $get("add_template_id"))) return "";
                        return __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.template_has_existing_fields_description");
                    })
                    ->disabled(function(Get $get){
                        $templateID = $get("add_template_id");
                        if(is_null($templateID)) return true;
                        return $this->isTemplateDisabled($templateID,$get);
                    })
                    ->action(function ($set, Get $get) {
                        $templateId = $get("add_template_id");
                        $data=["template_id" => $templateId];
                        EditCustomFormHelper::setCustomFieldInRepeater($data, $get, $set);
                        $set("add_template_id", null);

                        $customFields = $get("custom_fields");

                        $identifiers = $this->getOverlappedIdentifier($customFields,$templateId);
                        if(sizeof($identifiers) == 0) return;
                        $this->deletingExistingFields($get, $set, $identifiers);

                    }),
            ]),
        ];
    }

    private function hasExistingFields(array $customFields, int $templateId):bool {
        return !empty($this->getOverlappedIdentifier($customFields, $templateId));
    }

    private function getOverlappedIdentifier(array $customFields, int $templateId): array {
        //Fields with the same identify key
        $usedFieldIdentifier = [];
        $customFieldWithIdentifyKey = EditCustomFormHelper::getFieldsWithProperty($customFields,"identifier");
        foreach ($customFieldWithIdentifyKey as $customField) $usedFieldIdentifier[] = $customField["identifier"];
        return CustomForm::cached($templateId)->customFields
            ->whereIn("identifier",$usedFieldIdentifier)
            ->pluck("identifier")
            ->toArray();
    }

    private function isTemplateDisabled($templateId, Get $get): bool {
        if($this->useTemplateUsedGeneralFields($templateId,$get)) return true;
        $customFields = $get("custom_fields");
        $templates = EditCustomFormHelper::getFieldsWithProperty($customFields,"template_id");
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);

        return in_array($templateId,$usedTemplateIds);
    }

    public function useTemplateUsedGeneralFields(int $templateId, Get $get): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = EditCustomFormHelper::getUsedGeneralFieldIds($get("custom_fields"));
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

    private function deletingExistingFields(Get $get, $set, array $overlappedIdentifier, string $prefix= ""): void {
        $toSet = [];
        foreach ($get($prefix. "custom_fields") as $key => $customField) {
            if(!empty($customField["custom_fields"])) {
                $subprefix = $prefix. "custom_fields." . $key . ".";
                $this->deletingExistingFields($get,$set,$overlappedIdentifier, $subprefix);
                $newFieldData = $get($prefix. "custom_fields." . $key);
                $newFieldData["identifier"] = uniqid();
                $toSet[$key] = $newFieldData;
            }else{
                if(empty($customField["identifier"])) $toSet[$key] = $customField;
                else if(!in_array($customField["identifier"],$overlappedIdentifier)) $toSet[$key] = $customField;
            }
        }
        $set($prefix. "custom_fields", $toSet);
    }
}
