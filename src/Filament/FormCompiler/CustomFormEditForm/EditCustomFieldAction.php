<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
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


    private static function getRawStateActionForm($action):array {
        //Get RawSate (yeah is possible)
        return array_values($action->getLivewire()->getCachedForms())[1]->getRawState();
    }

    private static function getEditCustomFormActionModalWith(array $state): string {
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

    public static function getEditCustomFieldAction(CustomForm $customForm): Action {
        return Action::make('edit')
            ->action(fn($get, $set, $data, $arguments) => EditCustomFormFieldFunctions::setCustomFieldInRepeater($data, $get, $set, $arguments))
            ->mutateFormDataUsing(fn(Action $action)=> self::getRawStateActionForm($action))
            ->fillForm(fn($state, $arguments) => $state[$arguments["item"]])
            ->closeModalByClickingAway(false)
            ->icon('heroicon-m-pencil-square')
            ->label("Bearbeiten") //ToDo Translate
            ->modalWidth(function(array $state, array $arguments){
                return EditCustomFieldAction::getEditCustomFormActionModalWith($state[$arguments["item"]]);
            })
            ->form(function(Get $get, $state, array $arguments) use ($customForm) : array {
                return EditCustomFieldForm::getCustomFieldSchema($state[$arguments["item"]], $customForm);
            })
            ->hidden(function(array $state, array $arguments): bool{
                $item = $state[$arguments["item"]];
                return array_key_exists("template_id",$item) &&!is_null($item["template_id"]);
            })
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $suffix = " Felddaten bearbeiten ";
                if (empty($data["general_field_id"])) return $data["name_de"] . $suffix; //ToDo Translate
                else return "G. ".GeneralField::cached($data["general_field_id"])->name_de. $suffix; //ToDo Translate
            });
    }


    private static function unsetAttributesForClone(array $data):array {
        unset($data["id"]);
        unset($data["created_at"]);
        unset($data["deleted_at"]);
        unset($data["updated_at"]);
        return $data;
    }

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public static function getTemplateDissolveAction(CustomForm $record): Action {
        return Action::make('dissolve')
            ->closeModalByClickingAway(false)
            ->icon('carbon-sync-settings')
            ->color(Color::hex("#de9310"))
            ->label("Auflösen")//ToDo Translate
            ->requiresConfirmation()
            ->visible(function(array $state, array $arguments): bool{
                $item = $state[$arguments["item"]];
                return array_key_exists("template_id",$item) &&!is_null($item["template_id"]);
            })
            ->modalHeading(function (array $state, array $arguments) {
                $data = $state[$arguments["item"]];
                $template = CustomForm::cached($data["template_id"]);
                $name = $template->short_title;

                return "Möchten sie Wirklich das Template '" . $name . "'  auflösen?"; //ToDo Translate
            })
            ->action(function(Get $get, $set, array $state, array $arguments) use ($record) {
                $repeaterKey = $arguments["item"];
                $templateID = $state[$repeaterKey]["template_id"];
                $customFields = $get("custom_fields");

                $keyLocation = self::getKeyPosition($repeaterKey, $customFields);

                //Splitt the Fields
                $fieldsBeforeTemplate = array_slice($customFields, 0, $keyLocation,true);
                $fieldsAfterTemplate = array_diff_key($customFields, $fieldsBeforeTemplate);


                //Remove the Template Field
                unset($fieldsAfterTemplate[$repeaterKey]);

                //Get Template Fields
                $template = CustomForm::cached($templateID);
                $templateFields = $template->customFields;
                $templateFields = $templateFields->map(function (CustomField $field) use ($template, $record) {


                    //Clone Field
                    $fieldData = $field->toArray();

                    //Mutate Field Data's
                    $type = $field->getType();

                    //Load OptionData now, because it needs the field id
                    $fieldData = EditCustomFieldForm::mutateOptionData($fieldData, $template);
                    $fieldData = EditCustomFieldRule::mutateRuleDataOnLoad($fieldData, $template);

                    $fieldData = $type->mutateOnTemplateDissolve($fieldData, $field);

                    $fieldData = self::unsetAttributesForClone($fieldData);
                    $fieldData["custom_form_id"] = $record->id;

                    //Clone Ankers and Rules
                    $rules = $field->fieldRules;
                    $rulesCloned = [];
                    foreach ($fieldData["rules"] as $ruleData){

                        /**@var FieldRule $fieldRule*/
                        $fieldRule = $rules->where("id", $ruleData["id"])->first();

                        $ruleData = $fieldRule->toArray();

                        $ruleData = self::unsetAttributesForClone($ruleData);

                        $ruleData = $fieldRule->getAnchorType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);
                        $ruleData = $fieldRule->getRuleType()->mutateOnTemplateDissolve($ruleData,$fieldRule,$field);

                        unset($ruleData["custom_field_id"]);

                        //Set for Repeaters
                        $rulesCloned[uniqid()] = $ruleData;
                    }
                    $fieldData["rules"] = $rulesCloned;

                    return $fieldData;
                })->toArray();

                //Add unique Keys to the field's array
                $middleFields = [];
                foreach ($templateFields as $field){
                    $middleFields[uniqid()] = $field;
                }

                //Place the new fields there were the template was
                $combinedFields = array_merge($fieldsBeforeTemplate,$middleFields,$fieldsAfterTemplate);

                //Set the fields back in the repeater
                $set("custom_fields",$combinedFields);
            });
    }


    public static function getPullInLayoutAction(): Action {
        return Action::make("pullIn")
            ->icon('heroicon-m-arrow-long-up')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                $upperKey = array_keys($state)[$itemIndexPostion - 1];

                $newUpperState = $get("custom_fields.$upperKey.custom_fields");
                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("custom_fields.$upperKey.custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);

            })
            ->hidden(function ($arguments, $state) {
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                if ($itemIndexPostion == 0) return true;
                $upperCustomFieldData = $state[array_keys($state)[$itemIndexPostion - 1]];
                $type = EditCustomFormFieldFunctions::getFieldTypeFromRawDate($upperCustomFieldData);
                return !($type instanceof CustomLayoutType);
            });
    }

    private static function getKeyPosition($key, $array): int {
        //Position in Repeater
        $keys = array_keys($array);
        return array_search($key, $keys);
    }

    public static function getPullOutLayoutAction(): Action {
        return Action::make("pullOut")
            ->icon('heroicon-m-arrow-long-left')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $newUpperState = $get("../../custom_fields");

                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("../../custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);
            })
            ->hidden(function ($arguments, $state, $get) {
                return is_null($get("../../custom_fields"));
            });
    }

    private static function isTemplateDisabled($value, Get $get): bool {
        if(EditCustomFormFieldFunctions::useTemplateUsedGeneralFields($value,$get)) return true;
        $customFields = $get("custom_fields");
        $templates = EditCustomFormFieldFunctions::getFieldsWithProperty($customFields,"template_id");
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);
        return in_array($value,$usedTemplateIds);
    }

}
