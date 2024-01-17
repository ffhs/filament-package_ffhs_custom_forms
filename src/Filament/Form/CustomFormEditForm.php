<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CustomFormEditForm
{
    public static function formSchema(): array {
        return [

                Section::make("Form")
                    ->columns(3)
                    ->schema([
                        Fieldset::make()
                            ->columnStart(1)
                            ->columnSpan(1)
                            ->columns(1)
                            ->schema(self::getFieldAddActionSchema()),

                        Group::make()
                            ->columns(1)
                            ->columnSpan(2)
                            ->schema(fn(CustomForm $record)=>[
                                self::getCustomFieldRepeater($record)
                                    ->saveRelationshipsUsing(
                                        fn(Repeater $component, HasForms $livewire, ?array $state, CustomForm $record) =>
                                        self::saveCustomFields($component,$record,$state)
                                    )
                                ->rules([
                                    function (Get $get): Closure {
                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                            $formIdentifier = $get("custom_form_identifier");
                                            $requiredGeneralFieldForm = GeneralFieldForm::query()
                                                ->where("custom_form_identifier", $formIdentifier)
                                                ->select("general_field_id")
                                                ->where("is_required", true)
                                                ->with("generalField")
                                                ->get();

                                            $requiredGeneralIDs = $requiredGeneralFieldForm
                                                ->map(fn ($fieldForm) => $fieldForm->general_field_id);

                                            $usedGeneralIDs =self::getUsedGeneralFieldIds($value);
                                            $notAddedRequiredFields = $requiredGeneralIDs
                                                ->filter(fn($id)=> !in_array($id, $usedGeneralIDs));

                                            if($notAddedRequiredFields->count() == 0) return;

                                            $fieldName = $requiredGeneralFieldForm
                                                ->filter(function($fieldForm) use ($notAddedRequiredFields) {
                                                    $generalFieldId = $fieldForm->general_field_id;
                                                    $notAddedField = $notAddedRequiredFields->first();
                                                    return $generalFieldId == $notAddedField;
                                                })
                                                ->first()->generalField->name_de;

                                            $failureMessage =
                                                "Du must das generelle Feld \"" . $fieldName . "\" hinzufügen"; //ToDo Translate

                                            $fail($failureMessage);
                                        };
                                    },
                                ]),//ToDo Chanche to multy array
                        ])
                    ]),
            ];
    }


    private static function getCustomFieldRepeater(CustomForm $record): Repeater {
        return Repeater::make("custom_fields")
            ->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->relationship("customFieldInLayout")
            ->orderColumn("form_position")
            ->saveRelationshipsUsing(fn()=>empty(null))
            ->addable(false)
            ->defaultItems(0)
            ->columnSpan(2)
            ->persistCollapsed()
            ->reorderable()
            ->collapsed()
            ->collapsible(false)
            ->lazy()
            ->extraItemActions([
                self::getPullOutLayoutAction(),
                self::getPullInLayoutAction(),
                self::getEditCustomFormAction($record),
            ])
            ->itemLabel(function($state){
                $styleClassses = "text-sm font-medium ext-gray-950 dark:text-white  cursor-pointer truncate select-none ";
                $openOnClick = 'wire:click="mountFormComponentAction(\'data.custom_fields.record-20.custom_fields\', \'edit\', JSON.parse(\'{\u0022item\u0022:\u0022record-21\u0022}\'))"';
                 if(!empty($state["general_field_id"])){
                     $badge = new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)"));
                     $name = GeneralField::cached($state["general_field_id"])->name_de;
                     return new HtmlString(  '</h4>' .$badge. '<h4 class="'.$styleClassses.'"'.$openOnClick.'>' .$name); //ToDo Translate
                 }
                 else if(self::getFieldTypeFromRawDate($state) instanceof CustomLayoutType){
                    $size = sizeof($state["custom_fields"]);
                    $h4 = '<h4 x-on:click.stop="isCollapsed = !isCollapsed" class="'.$styleClassses.'">';
                    return new HtmlString(  "</h4>" .new HtmlBadge($size). $h4 .$state["name_de"]); //ToDo Translate
                 }
                 return  new HtmlString( '</h4> <h4 class="'.$styleClassses.'"'.$openOnClick.'">'. $state["name_de"] ); //ToDo Translate
               }
            )
            ->schema([
                Group::make()
                    ->schema(fn(Get $get)=>
                        !is_null($get("type")) && CustomFieldType::getTypeFromName(($get("type"))) instanceof CustomLayoutType?
                        [self::getCustomFieldRepeater($record)]: []
                    )
                    ->hidden(fn(Get $get)=>is_null($get("type")) || !CustomFieldType::getTypeFromName($get("type")) instanceof CustomFieldType)
            ]);
    }

    private static function getFieldAddActionSchema():array {
        return [
            //GeneralField
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            Select::make("add_general_field_id")
                ->label("")
                ->live()
                ->disableOptionWhen(function($value, Get $get) {
                    return in_array($value, self::getUsedGeneralFieldIds($get("custom_fields")));
                })
                ->options(function (Get $get){
                    $formIdentifier = $get("custom_form_identifier");
                    $generalFieldForms = GeneralFieldForm::query()
                        ->where("custom_form_identifier", $formIdentifier)
                        ->with("generalField")
                        ->get();
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
                    ->closeModalByClickingAway(false)
                    ->fillForm(fn()=> ["customFields"=>[0=>[]]])
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->label(fn()=>"Hinzufügen ") //ToDo Translate
                    ->disabled(fn(Get $get)=>
                        is_null($get("add_general_field_id")) ||
                        in_array($get("add_general_field_id"), self::getUsedGeneralFieldIds($get("custom_fields")))
                    )
                    ->mutateFormDataUsing(function(array $data,Get $get) {
                        //SetGeneralField ID
                        $state = $data["customFields"][0];
                        $id = ["general_field_id" => $get("add_general_field_id")];
                        return array_merge($state,$id );
                    })
                    ->form(fn(Get $get, CustomForm $record)=>
                        self::getCustomFieldSchema(
                            $record,
                            ["general_field_id" => $get("add_general_field_id")]
                        )
                    )
                    ->action(function ($set,Get $get,array $data){
                        $set("add_general_field_id", null);

                        $fields = $get("custom_fields");
                        $id = uniqid();
                        $fields[$id] = $data;
                        $set("custom_fields",$fields);
                    }),
            ]),

            //Space
            Placeholder::make("")->content(""),

            //New CustomField
            Placeholder::make("")
                ->label("Spezifische Felder") //ToDo Translate
                ->content(""),

            Select::make("add_custom_field_type")
                ->label("")
                ->live()
                ->options(function (Get $get){
                    $formIdentifier = $get("custom_form_identifier");
                    $formConfiguration = DynamicFormConfiguration::getFormConfigurationClass($formIdentifier);
                    $types = $formConfiguration::formFieldTypes();

                    $keys = array_map(fn($type) => $type::getFieldIdentifier(),$types);
                    $values = array_map(fn($type) => (new $type)->getTranslatedName(), $types);
                    return array_combine($keys,$values);
                }),
            Actions::make([
                Action::make("add_custom_field")
                    ->disabled(fn(Get $get)=>is_null($get("add_custom_field_type")))
                    ->fillForm(fn()=> ["customFields"=>[0=>[]]])
                    ->closeModalByClickingAway(false)
                    ->label("Hinzufügen") //ToDo Translate
                    ->modalWidth('5xl')
                    ->mutateFormDataUsing(function(array $data,Get $get) {
                        //setType
                        $state = $data["customFields"][0];
                        $type = ["type" => $get("add_custom_field_type")];
                        return array_merge($state, $type);
                    })
                    ->action(function ($set,Get $get,array $data){
                        $fields = $get("custom_fields");
                        $id = uniqid();
                        $fields[$id] = $data;
                        $set("custom_fields",$fields);
                    })
                    ->form(fn(Get $get, CustomForm $record)=>
                        self::getCustomFieldSchema(
                            $record,
                            ["type" => $get("add_custom_field_type")]
                        )
                    ),
            ])];


    }

    private static function getCustomFieldSchema(CustomForm $customForm, array $data):array{

        $hasVariations = $customForm->getFormConfiguration()::hasVariations();
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        //$isNew = !array_key_exists("id", $data);
        //$type =   $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);
        $type = self::getFieldTypeFromRawDate($data);

        return [
            Repeater::make("customFields")
                ->columns($isGeneral?1:2)
                ->reorderable(false)
                ->deletable(false)
                ->addable(false)
                ->defaultItems(0)
                ->columnSpanFull()
                ->label("")
                ->schema([

                    Group::make()
                        ->schema([
                            Tabs::make()
                                ->columnStart(1)
                                ->visible(!$isGeneral)
                                ->tabs([
                                    self::getTranslationTab("de","Deutsch"),
                                    self::getTranslationTab("en","Englisch"),
                                ]),

                            Toggle::make("has_variations")
                                ->label("Hat Variationen")
                                ->hidden(!$hasVariations)
                                ->columnStart(1)
                                ->live(),

                        ])->hidden($isGeneral),

                    Group::make()
                        ->schema([
                            Toggle::make("has_variations")
                                ->hidden(!$hasVariations || !$isGeneral)
                                ->label("Hat Variationen")
                                ->columnStart(1)
                                ->live(),

                            Tabs::make()
                                ->columnStart(1)
                                ->tabs(function (Get $get,$set) use ($customForm, $type, $isGeneral) {
                                    $tabs = [];

                                    //Default Tab
                                    $tabTitle = "Default";
                                    $id = null;
                                    $tabs[] = self::getCustomFieldVariationTab($id,$isGeneral,$type,$tabTitle);//ToDo Translate

                                    if(empty($get("variation-"))){
                                        $toSet = [
                                            0 => $type->prepareOptionDataBeforeFill([
                                                'is_active' => true,
                                                'required' => true,
                                            ]),
                                        ];
                                        $set("variation-", $toSet);
                                    }


                                    //If no Variations than skip the Variations and get only the default Tab
                                    if(!$get("has_variations")) return $tabs;

                                    //VariationTabs
                                    foreach ($customForm->variationModelsChached() as $model){
                                        if($customForm->getFormConfiguration()::isVariationHidden($model)) continue;

                                        $tabTitle = $customForm->getFormConfiguration()::variationName($model);
                                        $isDisabled =$customForm->getFormConfiguration()::isVariationDisabled($model);
                                        $varID = $model->id;

                                        //Create Tab
                                        $tabs[] = self::getCustomFieldVariationTab($varID, $isGeneral, $type, $tabTitle, $isDisabled);


                                        //Set contend if empty
                                        if(!empty($get("variation-".$varID))) continue;
                                        $toSet = [
                                            0 => $type->prepareOptionDataBeforeFill([
                                                'is_active' => !$isDisabled,
                                                'required' => !$isDisabled,
                                            ]),
                                        ];
                                        $set("variation-".$varID, $toSet);
                                    }

                                    return $tabs;
                                }),
                        ]),
                ]),
        ];
    }

    private static function getCustomFieldVariationTab(?int $variationId, bool $isGeneral, CustomFieldType $type, String $tabTitle, bool $isDisabled = false): Tab
    {
        $isTemplate = is_null($variationId);

        return  Tabs\Tab::make($tabTitle)
            ->schema([
                Repeater::make("variation-".$variationId)
                    ->reorderable(false)
                    ->deletable(false)
                    ->addable(false)
                    ->disabled($isDisabled)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->label("")
                    ->cloneable()
                    ->live()
                    ->schema([
                        self::getCustomFieldVariationRepeaterSchema($isGeneral, $type,$isDisabled),
                    ])
                    ->cloneAction(
                        fn(Action $action) => $action
                            ->color(fn() => $isTemplate? Color::Zinc: Color::Orange)
                            ->disabled($isTemplate || $isDisabled)
                            ->label("Vom Template")
                            ->action(function ($set,Get $get) use ($type, $variationId, $isGeneral) {
                                $template = array_values($get("variation-"))[0];
                                $recordName = array_keys($get("variation-".$variationId))[0];
                                $setPrefix = "variation-".$variationId.".".$recordName.".";

                                $set($setPrefix.'required', $template['required']);
                                $set($setPrefix.'is_active', $template['is_active']);

                                //FieldOptions clone
                                $clonedFieldOptions = $type->prepareCloneOptions($template['options'],$isGeneral);
                                $set($setPrefix.'options', $clonedFieldOptions);
                            })
                    ),
            ]);
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

    private static function getCustomFieldVariationRepeaterSchema(bool $isGeneral, CustomFieldType $type, bool $isDisabled):Group {

        return Group::make([
            //Settings
            Group::make()
                ->columnStart(1)
                ->schema([

                    Section::make()
                        ->columns()
                        ->schema([
                            // Active
                            Toggle::make('is_active')
                                ->label("Aktive") //ToDo Translate
                                ->default(!$isDisabled),

                            // Required
                            Toggle::make('required')
                                ->label("Benötigt") //ToDo Translate
                                ->default(!$isDisabled),
                        ]),

                    //Type Options
                    Group::make()
                        ->schema(function (Get $get, $set) use ($type, $isGeneral) {
                            $repeater = $type->getExtraOptionsRepeater();

                            if(is_null($repeater)) return [];

                            //if a new Variation was added, then we have to add options
                            $fieldOptions = $get("options");
                            $fieldOptionsNotExist = empty($fieldOptions) ;

                            $fieldOptionsEmpty = $fieldOptionsNotExist || sizeof($fieldOptions) == 1 &&
                                array_key_exists("options", $fieldOptions) &&
                                empty($fieldOptions["options"]);

                            if($fieldOptionsEmpty)
                                 $set("options", [$type->getExtraOptionFields()]);


                            return [$repeater];
                        })
                        ->visible($type->hasExtraOptions()),
                ])
                ->columns(1),
        ]);
    }

    private static function updateCustomField(CustomField $customfield,array $itemData, CustomForm $customForm): void {
        $customFieldData = array_filter($itemData, fn($key) =>!str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);
        $variations = array_filter($itemData, fn($key) => str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);

        $customfield->fill($customFieldData)->save();



        if(empty($variations)) {
            return;
        }  //If it is empty, it has also no Template variation what mean it wasn't edit

        $variationsOld = $customfield->customFieldVariation;
        $updatetVariationIds = [];

        $formConfiguration = $customForm->getFormConfiguration();

        foreach($variations as $variationName => $variationData){
            $variationData = $variationData[0];
            $variationId = explode("variation-",$variationName)[1]; //$variationName
            if($variationId == "") $variationId = null;
            else $variationId = intval($variationId);

            /** @var CustomFieldVariation|null $variation */
            $variation = $variationsOld
                ->filter(fn(CustomFieldVariation $fieldVariation)=> $fieldVariation->variation_id == $variationId)
                ->first();

            if($variation == null){
                //Prepare Variation Data
                $variationData = $customfield->getType()->prepareOptionDataBeforeCreate($variationData);
                //Create new Variation
                $variation = new CustomFieldVariation();

                $variation->variation_id = $variationId;
                $variation->variation_type = $formConfiguration::variationModel();
                $variation->custom_field_id = $customfield->id;
            }else{
                //Prepare Variation Data
                $variationData = $customfield->getType()->prepareOptionDataBeforeSave($variationData);
            }

            $variation->fill($variationData);
            $variation->save();

            $updatetVariationIds[] = $variationId;
        }

        //Delete the deleted Variation
        $variationsOld
            ->filter(fn(CustomFieldVariation $variation)=>!in_array($variation->variation_id,$updatetVariationIds))
            ->each(fn(CustomFieldVariation $variation)=>$variation->delete());


    }

    private static function createCustomField(array $itemData,CustomForm $customForm): void {
        $customField = new CustomField();
        $customField->identify_key = uniqid();
        self::updateCustomField($customField, $itemData, $customForm);
    }


    private static function setArrayExistingRecordFromArrayData(Collection &$customFieldsOld, array $state,  array&$statedRecords): void {
        foreach ($state as $key => $fieldData){
            if(!empty($fieldData["id"])){
                $record = $customFieldsOld->firstWhere("id", $fieldData["id"]);
                if(!is_null($record)) $statedRecords[$key]= $record;
            }

            if(empty($fieldData["custom_fields"])) continue;
            self::setArrayExistingRecordFromArrayData($customFieldsOld, $fieldData["custom_fields"], $statedRecords);
        }
    }

    //Copied from Repeaters and edited
    private static function saveCustomFields(Repeater $component, CustomForm $customForm, array $state): void {


        $relationship = $customForm->customFields();

        $existingRecords = $customForm->customFields;
        $statedRecords = [];
        self::setArrayExistingRecordFromArrayData($customForm->customFields, $state,$statedRecords);

        //ToDo Modify CustomField in CustomField

        $recordsToDelete = [];

        foreach (collect($existingRecords)->pluck($relationship->getRelated()->getKeyName()) as $keyToCheckForDeletion) { //ToDo Make
            if (array_key_exists("record-$keyToCheckForDeletion", $statedRecords)) {
                continue;
            }
            $recordsToDelete[] = $keyToCheckForDeletion;
        }

        $relationship
            ->whereKey($recordsToDelete)
            ->get()
            ->each(static fn(Model $record) =>  $record->delete());

        $childComponentContainers = $component->getChildComponentContainers();
        foreach ($childComponentContainers as $itemKey => $item) {
            // Perform some operation on $value here
            $childComponentContainers[$itemKey] =$item->getRawState();
        }

        self::saveCustomFieldFromData(1,$childComponentContainers,$customForm, $relationship,$statedRecords);

    }

    private static function saveCustomFieldFromData (int  $itemOrderRaw, array $itemInformation, CustomForm $customForm, HasMany $relationship, array &$existingRecords) {
        $itemOrder = $itemOrderRaw;
        foreach ($itemInformation as $itemKey => $itemData) {

            $itemData["form_position"] = $itemOrder;
            $itemOrder++;

            $isGeneralField = !empty($itemData["general_field_id"]);

            //For the Layouts
            if(!empty($itemData["custom_fields"])){
                $itemOrder = self::saveCustomFieldFromData($itemOrder, $itemData["custom_fields"], $customForm,$relationship,$existingRecords);
                unset($itemData["custom_fields"]);
                $itemData["layout_end_position"] = $itemOrder-1;
            }
            else if(!$isGeneralField && CustomFieldType::getTypeFromName($itemData["type"]) instanceof CustomLayoutType){
                unset($itemData["custom_fields"]);
                $itemData["layout_end_position"] = $itemOrder-1;
            }

            if ($record = ($existingRecords[$itemKey] ?? null)) {
                self::updateCustomField($record, $itemData,$customForm);
                continue;
            }
            $itemData["custom_form_id"] = $customForm->id;
            self::createCustomField($itemData,$customForm);
        }
        return $itemOrder;
    }

    private static function getUsedGeneralFieldIds(array $customFields):array {
        $usedGeneralFields = array_filter(
            array_values($customFields),
            fn($field)=> !empty($field["general_field_id"])
        );
        $nestedFields = collect(array_values($customFields))
            ->filter(fn($field)=> !empty($field["custom_fields"]))
            ->map(fn($field)=> $field["custom_fields"]);


        $usedGeneralFields=  array_filter($usedGeneralFields, fn($value)=> !is_null($value));

        if($nestedFields->count() > 0){
            $nestedGeneralFields = $nestedFields->map(fn(array $fields)=> self::getUsedGeneralFieldIds($fields))->flatten(1);
            return array_merge(array_map(fn($used) => $used["general_field_id"],$usedGeneralFields), $nestedGeneralFields->toArray());
        }

        return array_map(fn($used) => $used["general_field_id"],$usedGeneralFields);
    }

    private static function getEditCustomFormAction(CustomForm $customForm): Action {
        return Action::make('edit')
            ->closeModalByClickingAway(false)
            ->icon('heroicon-m-pencil-square')
            ->modalWidth(function(array $state,array $arguments){
                return empty($state[$arguments["item"]]["general_field_id"])?'5xl':'xl';
            })
            ->modalHeading(function(array $state,array $arguments){
                $data = $state[$arguments["item"]];
                if(!empty($data["general_field_id"]))
                    return "G. " . GeneralField::cached($data["general_field_id"])->name_de . " Felddaten bearbeiten"; //ToDo Translate
                else
                    return $data["name_de"] . " Felddaten bearbeiten "; //ToDo Translate
            })
            ->form(fn(Get $get, array $state,array $arguments)=>
                self::getCustomFieldSchema(
                    $customForm,
                    $state[$arguments["item"]]
                )
            )
            ->action(function (Get $get,$set,array $data,array $arguments): void {
                $fields = $get("custom_fields");
                $fields[$arguments["item"]] = $data["customFields"][0];
                $set("custom_fields",$fields);
            })
            ->fillForm(function($state,$arguments) use ($customForm) {
                $data = $state[$arguments["item"]];

                $customFieldData = array_filter(
                    $data,
                    fn($key) =>!str_starts_with($key, "variation-"),
                    ARRAY_FILTER_USE_KEY
                );
                $variations = array_filter(
                    $data,
                    fn($key) => str_starts_with($key, "variation-"),
                    ARRAY_FILTER_USE_KEY
                );

                if(empty($variations)){
                    $variations = [];
                    $customField = CustomField::cachedAllInForm($customForm->id)->firstWhere("id",$data["id"]);

                    foreach ($customField->customFieldVariation as $variation){
                        $variationData = $customField->getType()
                            ->prepareOptionDataBeforeFill($variation->toArray());
                        $varIdentifier = "variation-" . $variation->variation_id;
                        $variations[$varIdentifier] = [0=>$variationData];
                    }
                }

                return ["customFields"=> [array_merge($customFieldData,$variations)]];
            });
    }

    private static function getPullInLayoutAction(): Action {
        return Action::make("pullIn")
            ->icon('heroicon-m-arrow-long-up')
            ->action(function(array $arguments,array $state, $set, Get $get){
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                $upperKey = array_keys($state)[$itemIndexPostion-1];

                $newUpperState = $get("custom_fields.$upperKey.custom_fields");
                $newUpperState[$itemIndex] =$state[$itemIndex];
                $set("custom_fields.$upperKey.custom_fields",$newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields" , $newState);

            })
            ->hidden(function($arguments,$state) {
                $itemIndex = $arguments["item"];
                $itemIndexPostion = self::getKeyPosition($itemIndex, $state);
                if($itemIndexPostion == 0) return true;
                $upperCustomFieldData = $state[array_keys($state)[$itemIndexPostion-1]];
                $type = self::getFieldTypeFromRawDate($upperCustomFieldData);
                return !($type instanceof CustomLayoutType);
            });
    }

    private static function getPullOutLayoutAction(): Action {
        return Action::make("pullOut")
            ->icon('heroicon-m-arrow-long-left')
            ->action(function(array $arguments,array $state, $set, Get $get){
                $itemIndex = $arguments["item"];
                $newUpperState =  $get("../../custom_fields");

                $newUpperState[$itemIndex] =$state[$itemIndex];
                $set("../../custom_fields",$newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields" , $newState);

            })
            ->hidden(function($arguments,$state, $get) {
               return is_null($get("../../custom_fields"));
            });
    }

    private static function getFieldTypeFromRawDate(array $data): ?CustomFieldType {
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        return $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);
    }

    private static function getKeyPosition($key, $array):  int {
        $keys = array_keys($array);
        return array_search($key, $keys);
    }

}
