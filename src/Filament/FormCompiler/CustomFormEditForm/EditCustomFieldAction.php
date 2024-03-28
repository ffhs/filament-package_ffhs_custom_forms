<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
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
                ->native(false)
                ->label("")
                ->live()
                ->disableOptionWhen(function ($value, Get $get) {
                    $templateGenIds = CustomForm::cached($value)->generalFields->pluck("id")->toArray();
                    $existingIds = CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields"));
                    $commonValues = array_intersect($templateGenIds, $existingIds);

                    return !empty($commonValues);
                })
                ->options($templateOptions),
            Actions::make([
                Action::make("add_template")
                    ->form([]) //ToDo Make Template Form (Needs it realy?)
                    ->mutateFormDataUsing(fn(Action $action) => array_values($action->getLivewire()->getCachedForms())[1]->getRawState())//Get RawSate (yeah is possible)
                    ->fillForm(fn($get) => ["template_id" => $get("add_template_id")])
                    ->closeModalByClickingAway(false)
                    ->label(fn() => "Hinzufügen ") //ToDo Translate
                    ->disabled(fn(Get $get) => is_null($get("add_template_id"))
                       /* || collect(CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")))
                            ->contains($get("add_general_field_id")) ToDO Check GeneralFields*/
                    )
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        $data["template_id"] = $get("add_template_id");

                        self::setCustomField($data, $get, $set);
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
                    return in_array($value, CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")));
                }),

            Actions::make([
                Action::make("add_general_field")
                    ->modalWidth(fn(Get $get) => self::getEditCustomFormActionModalWith(["general_field_id" => $get("add_general_field_id")]))
                    ->form(fn(Get $get,
                        CustomForm $record) => EditCustomFieldForm::getCustomFieldSchema(["general_field_id" => $get("add_general_field_id")],
                        $record))
                    ->mutateFormDataUsing(fn(Action $action) => array_values($action->getLivewire()->getCachedForms())[1]->getRawState())//Get RawSate (yeah is possible)
                    ->fillForm(fn($get) => [
                        "is_active" => true,
                        "general_field_id" => $get("add_general_field_id"),
                        "options" => GeneralField::cached($get("add_general_field_id"))->getType()->getDefaultTypeOptionValues(),
                    ])
                    ->closeModalByClickingAway(false)
                    ->label(fn() => "Hinzufügen ") //ToDo Translate
                    ->disabled(fn(Get $get) => is_null($get("add_general_field_id")) ||
                        collect(CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")))
                            ->contains($get("add_general_field_id"))
                    )
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        self::setCustomField($data, $get, $set);
                        $set("add_general_field_id", null);
                    }),
            ]),
        ]);
    }

    private static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditForm::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired() || $type->canBeDeactivate() || $type->hasExtraTypeOptions();
        if (!$hasOptions) return 'xl';
        return '5xl';
    }

    private static function setCustomField(array $data, Get $get, $set, ?array $arguments = null): void {
        // $type = CustomFormEditForm::getFieldTypeFromRawDate($data);
        // $data = self::mutateOptionFieldData($type,$data,false);
        $fields = $get("custom_fields");
        if (is_null($arguments)) $fields[uniqid()] = $data;
        else $fields[$arguments["item"]] = $data;
        $set("custom_fields", $fields);
    }

    private static function getAddCustomFielActions(CustomForm $record): Group {
        $actions = [];
        $types = collect($record->getFormConfiguration()::formFieldTypes())->map(fn($class) => new $class());

        /**@var CustomFieldType $type */
        foreach ($types as $type) {
            $actions[] = Actions::make([
                Action::make("add_".$type::getFieldIdentifier()."_action")
                    ->modalHeading("Hinzufügen eines ".$type->getTranslatedName()." Feldes") //ToDo Translate
                    ->tooltip($type->getTranslatedName())
                    ->extraAttributes(["style" => "width: 100%; height: 100%;"])
                    ->label(new HtmlString(
                        '<div class="flex flex-col items-center justify-center">'. //
                        Blade::render(
                            '<x-'.$type->icon().
                            ' class="h-6 w-6 text-red-600"/>'
                        ).
                        '<p class="" style="margin-top: 10px;word-break: break-word;">'.$type->getTranslatedName().'</p>'.
                        '</div>'
                    ))
                    ->outlined()
                    ->mutateFormDataUsing(fn(Action $action) => array_values($action->getLivewire()->getCachedForms())[1]->getRawState())//Get RawSate (yeah is possible)
                    ->form(fn(Get $get) => EditCustomFieldForm::getCustomFieldSchema(["type" => $type::getFieldIdentifier()],
                        $record))
                    ->modalWidth(fn(Get $get) => self::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]))
                    ->disabled(fn(Get $get) => is_null($type::getFieldIdentifier()))
                    ->fillForm(fn($get) => [
                        "type" => $type::getFieldIdentifier(),
                        "options" => $type->getDefaultTypeOptionValues(),
                        "is_active" => true,
                    ])
                    ->closeModalByClickingAway(false)
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        $data["identify_key"] = uniqid();
                        self::setCustomField($data, $get, $set);
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

    public static function getEditCustomFieldAction(CustomForm $customForm): Action {
        return Action::make('edit')
            ->action(fn($get, $set, $data, $arguments) => self::setCustomField($data, $get, $set, $arguments))
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
            ->mutateFormDataUsing(function(Action $action):array { //Get RawSate
                return array_values($action->getLivewire()->getCachedForms())[1]->getRawState();
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

    public static function getTemplateDissolveAction(): Action {
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

                return "Möchten sie Wirklich '" . $name . "' Template auflösen?"; //ToDo Translate
            })
            ->action(function(Get $get, $set, array $data, array $arguments) {
                //ToDo make dissolve action
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
                $type = CustomFormEditForm::getFieldTypeFromRawDate($upperCustomFieldData);
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


}
