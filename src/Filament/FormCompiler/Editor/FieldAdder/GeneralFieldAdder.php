<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components\CustomFieldEditModal;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Cache;

final class GeneralFieldAdder extends FormEditorFieldAdder
{

    function getTitle(): string {
        return __("filament-package_ffhs_custom_forms::custom_forms.form.compiler.general_fields");
    }


    function getSchema(): array {
       return [

           Select::make("add_general_field_id")
               ->options(fn($get)=> self::getGeneralFieldSelectOptions($get))
               ->native(false)
               ->label("")
               ->live()
               ->disableOptionWhen(function ($value, Get $get) {
                   $usedGenIds = EditCustomFormHelper::getUsedGeneralFieldIds($get("custom_fields"));
                   return in_array($value, $usedGenIds);
               }),

            Actions::make([
                Action::make("add_general_field")
                    ->mutateFormDataUsing(fn(Action $action)=> EditCustomFormHelper::getRawStateForm($action,1))
                    ->label(fn() => __("filament-package_ffhs_custom_forms::custom_forms.functions.add"))
                    ->closeModalByClickingAway(false)
                    ->modalWidth(function(Get $get)  {
                        $state = ["general_field_id" => $get("add_general_field_id")];
                        return CustomFieldEditModal::getEditCustomFormActionModalWith($state);
                    })
                    ->form(function(Get $get, CustomForm $record){
                        $state = ["general_field_id" => $get("add_general_field_id")];
                        return [CustomFieldEditModal::make($record,$state)];
                    })
                    ->fillForm(fn($get) => [
                        "is_active" => true,
                        "general_field_id" => $get("add_general_field_id"),
                        "options" => GeneralField::cached($get("add_general_field_id"))->getType()->getDefaultTypeOptionValues(),
                    ])
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        EditCustomFormHelper::setCustomFieldInRepeater($data, $get, $set);
                        $set("add_general_field_id", null);
                    })
                    ->disabled(function(Get $get):bool{
                        // Disable if no id is Selected or if it is already imported
                        if(is_null($get("add_general_field_id"))) return true;
                        $usedGenIds = EditCustomFormHelper::getUsedGeneralFieldIds($get("custom_fields"));
                        return collect($usedGenIds)->contains($get("add_general_field_id"));
                    }),
            ]),
        ];
    }


    private static function getGeneralFieldSelectOptions(Get $get) {
        $formIdentifier = $get("custom_form_identifier");

        $generalFieldForms = Cache::remember("general_filed_form-from-identifier_".$formIdentifier, 5,
            fn() => GeneralFieldForm::query()
                ->where("custom_form_identifier", $formIdentifier)
                ->with("generalField")
                ->get()
        );

        //Mark Required GeneralFields
        $generalFields = $generalFieldForms->map(function (GeneralFieldForm $generalFieldForm) {
            $generalField = $generalFieldForm->generalField;

            if ($generalFieldForm->is_required) {
                $generalField->name = "* ".$generalField->name;
                $generalField->name = "* ".$generalField->name;
            }
            return $generalField;
        });

        return $generalFields->pluck("name", "id"); //ToDo Translate
    }

}
