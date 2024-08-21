<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

class  AddTemplateFieldAction extends Action
{

    protected $option;

    protected function setUp(): void {
        parent::setUp();
        $this->closeModalByClickingAway(false)
            ->label(fn() => __("filament-package_ffhs_custom_forms::custom_forms.functions.add"))
            ->requiresConfirmation(fn($arguments , $state) => $this->hasExistingFields($state("custom_fields"), $this->getOption()))
            ->modalHeading(function ($state, $arguments){
                if(!$this->hasExistingFields($state, $this->getOption())) return "";
                return __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.template_has_existing_fields");
            })
            ->modalDescription(function ($arguments, $state){
                if(!$this->hasExistingFields($state, $this->getOption())) return "";
                return __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.template_has_existing_fields_description");
            });
        $this->action($this->createField(...));
    }


    public function createField(array $arguments, $set, $component, $get){
        $templateId = $this->getOption();

        $field = [
            "template_id" =>  $templateId,
            "is_active" => true,
        ];
        FormEditorFieldAdder::addNewField($component, $arguments, $field);



        $customFields = $get($component->getStatePath(true) . '.custom_fields', true);

        $identifiers = $this->getOverlappedIdentifier($customFields,$templateId);
        if(sizeof($identifiers) == 0) return;
        $this->deletingExistingFields($get, $set, $identifiers);
    }



    private function hasExistingFields(array $customFields, int $templateId):bool {
        return !empty($this->getOverlappedIdentifier($customFields, $templateId));
    }

    private function getOverlappedIdentifier(array $customFields, int $templateId): array {
        //Fields with the same identify key
        $usedFieldIdentifier = [];
        $customFields = array_filter($customFields, fn($field) => !empty($customFields["identifier"]));
        $customFieldWithIdentifyKey = array_combine(array_map(fn($field) => $field["identifier"], $customFields), $customFields);

        foreach ($customFieldWithIdentifyKey as $customField) $usedFieldIdentifier[] = $customField["identifier"];
        return CustomForm::cached($templateId)->customFields
            ->whereIn("identifier",$usedFieldIdentifier)
            ->pluck("identifier")
            ->toArray();
    }

    private function deletingExistingFields(Get $get, $set, array $overlappedIdentifier, string $prefix= ""): void {


        //TODO MAKE FOR NEW SYSTEM

        $toSet = [];
        foreach ($get($prefix. "custom_fields") as $key => $customField) {
            if(!empty($customField["custom_fields"])) {
                $subPrefix = $prefix. "custom_fields." . $key . ".";
                $this->deletingExistingFields($get,$set,$overlappedIdentifier, $subPrefix);
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


    public function getOption()
    {
        return $this->evaluate($this->option);
    }


    public function option($option): static
    {
        $this->option = $option;
        return $this;
    }




}
