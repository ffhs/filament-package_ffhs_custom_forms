<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\MountableAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Contracts\Support\Htmlable;

final class TemplateAdder extends FormEditorFieldAdder
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
                    ->requiresConfirmation(fn($get) => $this->hasExistingFields($get("custom_fields"),$get("add_template_id")))
                    ->modalHeading(function ($get){
                        if(!$this->hasExistingFields($get("custom_fields"), $get("add_template_id"))) return "";
                        return "Es gibt Felder die ursprünglich von diesem Template stammen"; //ToDo Translate
                    })
                    ->modalDescription(function ($get){
                        if(!$this->hasExistingFields($get("custom_fields"), $get("add_template_id"))) return "";
                        return "Es gibt Felder die ursprünglich von diesem Template stammen. Diese Felder werden von
                            diesem Formular gelöscht und die existierenden Antworten übernommen"; //ToDo Translate
                    })
                    ->disabled(function(Get $get){
                        $templateID = $get("add_template_id");
                        if(is_null($templateID)) return true;
                        return $this->isTemplateDisabled($templateID,$get);
                    })
                    ->action(function ($set, Get $get) {
                        $templateId = $get("add_template_id");
                        $data=["template_id" => $templateId];
                        CustomFormEditorHelper::setCustomFieldInRepeater($data, $get, $set);
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
        $customFieldWithIdentifyKey = CustomFormEditorHelper::getFieldsWithProperty($customFields,"identify_key");
        foreach ($customFieldWithIdentifyKey as $customField) $usedFieldIdentifier[] = $customField["identify_key"];
        return CustomForm::cached($templateId)->customFields
            ->whereIn("identify_key",$usedFieldIdentifier)
            ->pluck("identify_key")
            ->toArray();
    }

    private function isTemplateDisabled($templateId, Get $get): bool {
        if($this->useTemplateUsedGeneralFields($templateId,$get)) return true;
        $customFields = $get("custom_fields");
        $templates = CustomFormEditorHelper::getFieldsWithProperty($customFields,"template_id");
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);

        return in_array($templateId,$usedTemplateIds);
    }

    public function useTemplateUsedGeneralFields(int $templateId, Get $get): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = CustomFormEditorHelper::getUsedGeneralFieldIds($get("custom_fields"));
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
                $newFieldData["identify_key"] = uniqid();
                $toSet[$key] = $newFieldData;
            }else{
                if(empty($customField["identify_key"])) $toSet[$key] = $customField;
                else if(!in_array($customField["identify_key"],$overlappedIdentifier)) $toSet[$key] = $customField;
            }
        }
        $set($prefix. "custom_fields", $toSet);
    }
}
