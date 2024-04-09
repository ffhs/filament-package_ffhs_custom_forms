<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class EditCustomFieldAction
{

    public static function getFieldAddActionSchema(CustomForm $record): array {

        $useTemplates = config("ffhs_custom_forms.use_templates");
        $showTemplates = $useTemplates && !$record->is_template ;

        return [
            //GeneralFields
            self::getGeneralFieldAddAction(),

            //Space
            Placeholder::make("")->content("")
                ->visible($showTemplates),

            //Templates
            self::getTemplateAddAction($record)
                ->visible($showTemplates),

            //Space
            Placeholder::make("")->content(""),

            //CustomField
            self::getAddCustomFielActions($record),

        ];
    }

    private static function getTemplateAddAction(CustomForm $record): Group  {

        $templateOptions =
            CustomForm::getTemplatesForFormType($record->getFormConfiguration())
                ->pluck("short_title", "id");

        return Group::make([
            //GeneralField Title
            Placeholder::make("")
                ->label("Template Felder") //ToDo Translate
                ->content(""),

            Select::make("add_template_id")
                ->disableOptionWhen(fn($value, Get $get)=>  self::isTemplateDisabled($value,$get))
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
                        return self::isTemplateDisabled($templateID,$get);
                    })
                    ->action(function ($set, Get $get) {
                        $data=["template_id" => $get("add_template_id")];
                        EditCustomFormFieldFunctions::setCustomFieldInRepeater($data, $get, $set);
                        $set("add_template_id", null);
                    }),
            ]),
        ]);
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
                $generalField->name_de = "* ".$generalField->name_de;
                $generalField->name_en = "* ".$generalField->name_en;
            }
            return $generalField;
        });

        return $generalFields->pluck("name_de", "id"); //ToDo Translate
    }

    private static function getGeneralFieldAddAction(): Group {
        return Group::make([
            //GeneralField Title
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            Select::make("add_general_field_id")
                ->options(fn($get)=> self::getGeneralFieldSelectOptions($get))
                ->native(false)
                ->label("")
                ->live()
                ->disableOptionWhen(function ($value, Get $get) {
                    $usedGenIds = EditCustomFormFieldFunctions::getUsedGeneralFieldIds($get("custom_fields"));
                    return in_array($value, $usedGenIds);
                }),

            Actions::make([
                Action::make("add_general_field")
                    ->mutateFormDataUsing(fn(Action $action)=> self::getRawStateActionForm($action))
                    ->label(fn() => "Hinzufügen ") //ToDo Translate
                    ->closeModalByClickingAway(false)
                    ->modalWidth(function(Get $get)  {
                        $state = ["general_field_id" => $get("add_general_field_id")];
                        return self::getEditCustomFormActionModalWith($state);
                    })
                    ->form(function(Get $get, CustomForm $record){
                        $state = ["general_field_id" => $get("add_general_field_id")];
                        return EditCustomFieldForm::getCustomFieldSchema($state,$record);
                    })
                    ->fillForm(fn($get) => [
                        "is_active" => true,
                        "general_field_id" => $get("add_general_field_id"),
                        "options" => GeneralField::cached($get("add_general_field_id"))->getType()->getDefaultTypeOptionValues(),
                    ])
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        EditCustomFormFieldFunctions::setCustomFieldInRepeater($data, $get, $set);
                        $set("add_general_field_id", null);
                    })
                    ->disabled(function(Get $get):bool{
                        //Disable if no id is Selected or if it is already imported
                        if(is_null($get("add_general_field_id"))) return true;
                        $usedGenIds = EditCustomFormFieldFunctions::getUsedGeneralFieldIds($get("custom_fields"));
                        return collect($usedGenIds)->contains($get("add_general_field_id"));
                    }),
            ]),
        ]);
    }


    public static function getRawStateActionForm($action):array {
        //Get RawSate (yeah is possible)
        return array_values($action->getLivewire()->getCachedForms())[1]->getRawState();
    }

    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = EditCustomFormFieldFunctions::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired() || $type->canBeDeactivate() || $type->hasExtraTypeOptions();
        if (!$hasOptions) return 'xl';
        return '5xl';
    }


    private static function getAddCustomFielActions(CustomForm $record): Group {
        $actions = [];
        $types = collect($record->getFormConfiguration()::formFieldTypes())->map(fn($class) => new $class());

        /**@var CustomFieldType $type */
        foreach ($types as $type) {
            $modalWidth  = self::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]);

            $actions[] = Actions::make([
                Action::make("add_".$type::getFieldIdentifier()."_action")
                    ->modalHeading("Hinzufügen eines ".$type->getTranslatedName()." Feldes") //ToDo Translate
                    ->disabled(fn(Get $get) => is_null($type::getFieldIdentifier()))
                    ->extraAttributes(["style" => "width: 100%; height: 100%;"])
                    ->label(self::getCustomFieldAddActionLabel($type))
                    ->closeModalByClickingAway(false)
                    ->tooltip($type->getTranslatedName())
                    ->modalWidth($modalWidth)
                    ->outlined()
                    ->mutateFormDataUsing(fn(Action $action)=> self::getRawStateActionForm($action))
                    ->form(function() use ($type, $record) {
                        $state = ["type" => $type::getFieldIdentifier()];
                        return EditCustomFieldForm::getCustomFieldSchema($state,$record);
                    })
                    ->fillForm(fn($get) => [
                        "type" => $type::getFieldIdentifier(),
                        "options" => $type->getDefaultTypeOptionValues(),
                        "is_active" => true,
                        "identify_key" => uniqid(),
                    ])
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        EditCustomFormFieldFunctions::setCustomFieldInRepeater($data, $get, $set);
                    }),
            ]);
        }
        return Group::make()
            ->columns()
            ->schema(
                array_merge([
                    //Title
                    Placeholder::make("")
                        ->label("Spezifische Felder") //ToDo Translate
                        ->columnSpanFull()
                        ->content(""),
                ], $actions)
            );
    }

    private static function getCustomFieldAddActionLabel(CustomFieldType $type):HtmlString {
        $html =
            '<div class="flex flex-col items-center justify-center">'. //
                 Blade::render('<x-'.$type->icon().' class="h-6 w-6 text-red-600"/>').
                '<p class="" style="margin-top: 10px;word-break: break-word;">'.$type->getTranslatedName().'</p>'.
            '</div>';

        return  new HtmlString($html);
    }


    private static function isTemplateDisabled($value, Get $get): bool {
        if(EditCustomFormFieldFunctions::useTemplateUsedGeneralFields($value,$get)) return true;
        $customFields = $get("custom_fields");
        $templates = EditCustomFormFieldFunctions::getFieldsWithProperty($customFields,"template_id");
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);
        return in_array($value,$usedTemplateIds);
    }

}
