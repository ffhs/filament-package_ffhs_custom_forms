<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class CustomFieldEditForm
{

    public static function getFieldAddActionSchema(CustomForm $record):array {

        return [
            //GeneralField
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            //New GeneralFields
            self::getGeneralfieldAddAction(),

            //Space
            Placeholder::make("")->content(""),
            //New CustomFields
            Placeholder::make("")
                ->label("Spezifische Felder") //ToDo Translate
                ->content(""),

            Group::make(self::getNewCustomFielActions($record))->columns()

        ];
    }





    private static function getTranslationTab(string $location, string $label): Tab {
        return Tab::make($label)
            ->schema([
                TextInput::make("name_" . $location)
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.name'))
                    ->required(),
                TextInput::make("tool_tip_" . $location)
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.tool_tip')),
            ]);
    }





    private static function getNewCustomFielActions(CustomForm $record): array {
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
                    ->form(fn(Get $get, CustomForm $record) => CustomFieldEditForm::getCustomFieldSchema(["type" => $type::getFieldIdentifier()]))
                    ->modalWidth(fn(Get $get) => self::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]))
                    ->disabled(fn(Get $get) => is_null($type::getFieldIdentifier()))
                    ->fillForm(fn($get) => [
                        "type" => $type::getFieldIdentifier(),
                        "options" => $type->getDefaultTypeOptionValues(),
                        "is_active"=> true,
                    ])
                    ->closeModalByClickingAway(false)
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        self::setCustomField($data,$get,$set);
                    })
            ]);
        }
        return $actions;
    }

    private static function getGeneralFieldAddAction():Group {
        return Group::make([
            Select::make("add_general_field_id")
                ->native(false)
                ->label("")
                ->live()
                ->disableOptionWhen(function($value, Get $get) {
                    return in_array($value, CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")));
                })
                ->options(function (Get $get){
                    $formIdentifier = $get("custom_form_identifier");

                    $generalFieldForms =  Cache::remember("general_filed_form-from-identifier_". $formIdentifier, 5,
                        fn()=>  GeneralFieldForm::query()
                            ->where("custom_form_identifier", $formIdentifier)
                            ->with("generalField")
                            ->get()
                    );

                    //Mark Required GeneralFields
                    $generalFields=  $generalFieldForms->map(function(GeneralFieldForm $generalFieldForm){
                        $generalField =$generalFieldForm->generalField;

                        if($generalFieldForm->is_required){
                            $generalField->name_de = "* " . $generalField->name_de;
                            $generalField->name_en = "* " . $generalField->name_en;
                        }
                        return $generalField;
                    });

                    return $generalFields->pluck("name_de","id"); //ToDo Translate
                }),
            Actions::make([
                    Action::make("add_general_field")
                        ->modalWidth(fn(Get $get)=> self::getEditCustomFormActionModalWith(["general_field_id"=> $get("add_general_field_id")]))
                        ->form(fn(Get $get, CustomForm $record)=> CustomFieldEditForm::getCustomFieldSchema(["general_field_id" => $get("add_general_field_id")]))
                        ->mutateFormDataUsing(fn(Action $action) =>array_values($action->getLivewire()->getCachedForms())[1]->getRawState())//Get RawSate (yeah is possible)
                        ->fillForm(fn($get)=> [
                            "is_active"=> true,
                            "general_field_id"=> $get("add_general_field_id"),
                            "options" => GeneralField::cached($get("add_general_field_id"))->getType()->getDefaultTypeOptionValues(),
                        ])
                        ->closeModalByClickingAway(false)
                        ->label(fn()=>"Hinzufügen ") //ToDo Translate
                        ->disabled(fn(Get $get)=>
                           is_null($get("add_general_field_id")) ||
                           collect(CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")))
                               ->contains($get("add_general_field_id"))
                        )
                        ->action(function ($set,Get $get,array $data) {
                            //Add to the other Fields
                            self::setCustomField($data,$get,$set);
                            $set("add_general_field_id", null);
                        })
            ]),
        ]);
    }



    public static function getEditCustomFieldAction(CustomForm $customForm): Action {
        return Action::make('edit')
            ->action(fn (Get $get,$set,array $data,array $arguments) => self::setCustomField($data,$get,$set,$arguments))
            ->closeModalByClickingAway(false)
            ->icon('heroicon-m-pencil-square')
            ->modalWidth(fn(array $state,array $arguments)=>
                CustomFieldEditForm::getEditCustomFormActionModalWith($state[$arguments["item"]])
            )
            ->form(fn(Get $get, array $state,array $arguments)=>
                CustomFieldEditForm::getCustomFieldSchema($state[$arguments["item"]])
            )
            ->mutateFormDataUsing(fn(Action $action) =>
                //Get RawSate
                array_values($action->getLivewire()->getCachedForms())[1]->getRawState()
            )
            ->modalHeading(function(array $state,array $arguments){
                $data = $state[$arguments["item"]];
                if(!empty($data["general_field_id"]))
                    return "G. " . GeneralField::cached($data["general_field_id"])->name_de . " Felddaten bearbeiten"; //ToDo Translate
                else
                    return $data["name_de"] . " Felddaten bearbeiten "; //ToDo Translate
            })
            ->fillForm(function($state,$arguments) use ($customForm) {

                $data = $state[$arguments["item"]];
                //$type = CustomFormEditForm::getFieldTypeFromRawDate($data);
                //self::mutateOptionFieldData($type,$data,true);

                return $data;
            });
    }

    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditForm::getFieldTypeFromRawDate($state);
        if(!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired()||$type->canBeDeactivate()||$type->hasExtraTypeOptions();
        if(!$hasOptions) return 'xl';
        return'5xl';
    }

    public static function getCustomFieldSchema(array $data):array{

        $isGeneral = array_key_exists("general_field_id",$data)&& !empty($data["general_field_id"]);
        $type = CustomFormEditForm::getFieldTypeFromRawDate($data);
        $columns = $isGeneral?1:2;

        return [
            Group::make()
                ->columns($columns)
                ->columnSpanFull()
                ->label("")
                ->schema([
                    Tabs::make()
                        ->columnStart(1)
                        ->hidden($isGeneral)
                        ->tabs([
                            self::getTranslationTab("de","Deutsch"),
                            self::getTranslationTab("en","Englisch"),
                        ]),

                    self::getFieldOptionSection($type)
                        ->columnSpan(1),

                    Section::make("Regeln"),//toDo Rules Section

                ]),
        ];
    }

    private static function getFieldOptionSection(CustomFieldType $type): Section {
        return Section::make("Optionen") //ToDo Translate
            ->schema([
                Fieldset::make()
                    ->schema([
                        Toggle::make('is_active')
                            ->visible($type->canBeDeactivate())
                            ->label("Aktive"), //ToDo Translate

                        // Required
                        Toggle::make('required')
                            ->visible($type->canBeRequired())
                            ->label("Benötigt"), //ToDo Translate

                    ]),
                Fieldset::make()
                    ->statePath("options")
                    ->visible($type->hasExtraTypeOptions())
                    ->schema($type->getExtraTypeOptionComponents())

            ]);
    }


    private static function setCustomField(array $data, Get $get, $set, ?array $arguments = null):void {
       // $type = CustomFormEditForm::getFieldTypeFromRawDate($data);
       // $data = self::mutateOptionFieldData($type,$data,false);
        $fields = $get("custom_fields");
        if(is_null($arguments)) $fields[uniqid()] = $data;
        else $fields[$arguments["item"]] = $data;
        $set("custom_fields",$fields);
    }


}
