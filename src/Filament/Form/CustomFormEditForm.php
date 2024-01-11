<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
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
use function PHPUnit\Framework\isEmpty;

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
                            ->columnSpan(2)
                            ->schema(fn(CustomForm $record) => [
                                Repeater::make("custom_fields")
                                    ->itemLabel(function($state){
                                        if(!empty($state["general_field_id"]))
                                            return "G. " . GeneralField::cached($state["general_field_id"])->name_de; //ToDo Translate
                                        else
                                            return $state["name_de"]; //ToDo Translate
                                    })
                                    ->orderColumn("form_position")
                                    ->relationship("customFields")
                                    ->reorderableWithDragAndDrop()
                                    ->addable(false)
                                    //->reorderableWithButtons()
                                    ->defaultItems(0)
                                    ->persistCollapsed()
                                    ->reorderable()
                                    ->collapsed()
                                    ->expandAllAction(fn(Action $action)=> $action->hidden())
                                    ->collapseAllAction(fn(Action $action)=> $action->hidden())
                                    ->extraItemActions([
                                        Action::make('edit')
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
                                            ->form(fn($get,$state,$arguments)=>
                                                self::getCustomFieldSchema(
                                                    $get("custom_form_identifier"),
                                                    $state[$arguments["item"]]
                                                )
                                            )
                                            ->action(function ($get,$set,$data,$arguments): void {
                                                $fields = $get("custom_fields");
                                                $fields[$arguments["item"]] = $data["customFields"][0];
                                                $set("custom_fields",$fields);
                                            })
                                            ->fillForm(function($state,$arguments)  {
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
                                                    $customField = CustomField::cached($data["id"]);

                                                    foreach ($customField->customFieldVariation as $variation){
                                                        $variationData = $customField->getType()
                                                            ->prepareOptionDataBeforeFill($variation->toArray());
                                                        $varIdentifier = "variation-" . $variation->variation_id;
                                                        $variations[$varIdentifier] = [0=>$variationData];
                                                    }
                                                }

                                                return ["customFields"=> [array_merge($customFieldData,$variations)]];
                                            }),
                                    ])
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
                                        }
                                    ])
                                    ->saveRelationshipsUsing(
                                        fn(Repeater $component, HasForms $livewire, ?array $state) =>
                                        self::saveCustomFields($component,$record,$state)
                                    ),

                            ]),
                    ]),

            ];
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
                ->disableOptionWhen(function($value, $get) {
                    return in_array($value, self::getUsedGeneralFieldIds($get("custom_fields")));
                })
                ->options(function ($get){
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
                    ->label(fn()=>"Erstellen ") //ToDo Translate
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->disabled(fn($get)=>
                        is_null($get("add_general_field_id")) ||
                        in_array($get("add_general_field_id"), self::getUsedGeneralFieldIds($get("custom_fields")))
                    )
                    ->fillForm(function ($get){
                        $type = GeneralField::cached($get("add_general_field_id"))->getType();
                        return ["customFields"=>[0=>["variation-"=> $type->prepareOptionDataBeforeFill([])]]];
                    })
                    ->mutateFormDataUsing(function($data,$get) {
                        //SetGeneralField ID
                        $state = $data["customFields"][0];
                        $id = ["general_field_id" => $get("add_general_field_id")];
                        return array_merge($state,$id );
                    })
                    ->form(function($get){
                        $formIdentifier = $get("custom_form_identifier");
                        $newGeneralFieldID = ["general_field_id" => $get("add_general_field_id")];
                        return self::getCustomFieldSchema(
                            $formIdentifier,
                            $newGeneralFieldID
                        );
                    })
                    ->action(function ($set,$get,array $data){
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
                ->options(function ($get){
                    $formIdentifier = $get("custom_form_identifier");
                    $formConfiguration = DynamicFormConfiguration::getFormConfigurationClass($formIdentifier);
                    $types = $formConfiguration::formFieldTypes();

                    $keys = array_map(fn($type) => $type::getFieldIdentifier(),$types);
                    $values = array_map(fn($type) => (new $type)->getTranslatedName(), $types);
                    return array_combine($keys,$values);
                }),
            Actions::make([
                Action::make("add_custom_field")
                    ->disabled(fn($get)=>is_null($get("add_custom_field_type")))
                    ->closeModalByClickingAway(false)
                    ->label("Erstellen") //ToDo Translate
                    ->modalWidth('5xl')
                    ->fillForm(function ($get){
                        $typeName = $get("add_custom_field_type");
                        $type = CustomFieldType::getTypeFromName($typeName);
                        return ["customFields"=>[0=>["variation-"=> $type->prepareOptionDataBeforeFill([])]]];
                    })
                    ->mutateFormDataUsing(function($data,$get) {
                        //setType
                        $state = $data["customFields"][0];
                        $type = ["type" => $get("add_custom_field_type")];
                        return array_merge($state, $type);
                    })
                    ->action(function ($set,$get,array $data){
                        $fields = $get("custom_fields");
                        $id = uniqid();
                        $fields[$id] = $data;
                        $set("custom_fields",$fields);
                    })
                    ->form(function($get){
                        $formIdentifier = $get("custom_form_identifier");
                        $type = ["type" => $get("add_custom_field_type")];
                        return self::getCustomFieldSchema(
                            $formIdentifier,
                            $type
                        );
                    }),
            ])];


    }

    private static function getCustomFieldSchema(string $formIdentifyer, array $data):array{

        $hasVariations = DynamicFormConfiguration::getFormConfigurationClass($formIdentifyer)::hasVariations();
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        //$isNew = !array_key_exists("id", $data);
        $type = $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);

        return [
            Repeater::make("customFields")
                ->reorderable(false)
                ->deletable(false)
                ->addable(false)
                ->defaultItems(0)
                ->columnSpanFull()
                ->label("")
                ->columns($isGeneral?1:2)
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

                            TextInput::make("identify_key") //ToDo check that it exist only one time in the form
                                ->label("Schlüssel") //ToDo Translate
                                ->required(),

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
                                ->tabs(function ($get,CustomForm $record,$set) use ($type, $isGeneral) {
                                    $tabs = [];

                                    //Default Tab
                                    $tabTitle = "Default";
                                    $id = null;
                                    $tabs[] = self::getCustomFieldVariationTab($id,$isGeneral,$type,$tabTitle);//ToDo Translate

                                    //If no Variations than skip the Variations and get only the default Tab
                                    if(!$get("has_variations")) return $tabs;

                                    //VariationTabs
                                    foreach ($record->variationModelsChached() as $model){
                                        if($record->getFormConfiguration()::isVariationHidden($model)) continue;

                                        $tabTitle = $record->getFormConfiguration()::variationName($model);
                                        $isDisabled =$record->getFormConfiguration()::isVariationDisabled($model);
                                        $varID = $model->id;

                                        //Create Tab
                                        $tabs[] = self::getCustomFieldVariationTab($varID, $isGeneral, $type, $tabTitle, $isDisabled);


                                        //Set contend if empty
                                        if(!empty($get("variation-".$varID))) continue;
                                        $toSet = [
                                            0 => $type->prepareOptionDataBeforeFill([
                                                    'is_active' => !$isDisabled,
                                                    'required' => !$isDisabled,
                                                ])
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
                            ->action(function ($set,$get) use ($type, $variationId, $isGeneral) {
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
                        ->schema(function ($get, $set) use ($type, $isGeneral) {
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

    private static function updateCustomField(CustomField $customfield,array $itemData, CustomForm $customForm):CustomField{
        $customFieldData = array_filter($itemData, fn($key) =>!str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);
        $variations = array_filter($itemData, fn($key) => str_starts_with($key, "variation-"),ARRAY_FILTER_USE_KEY);

        $customfield->fill($customFieldData)->save();

        if(empty($variations)) return $customfield;  //If it is empty, it has also no Template variation what mean it wasn't edit

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


        return $customfield;
    }

    private static function createCustomField(array $itemData,CustomForm $customForm):CustomField{
        return self::updateCustomField(new CustomField(), $itemData,$customForm);
    }

    //Copied from Repeaters and edited
    private static function saveCustomFields(Repeater $component, CustomForm $customForm, ?array $state): void {
        if (!is_array($state)) {
            $state = [];
        }

        $relationship = $component->getRelationship();

        $existingRecords = $component->getCachedExistingRecords();

        $recordsToDelete = [];

        foreach ($existingRecords->pluck($relationship->getRelated()->getKeyName()) as $keyToCheckForDeletion) {
            if (array_key_exists("record-$keyToCheckForDeletion", $state)) {
                continue;
            }

            $recordsToDelete[] = $keyToCheckForDeletion;
        }

        $relationship
            ->whereKey($recordsToDelete)
            ->get()
            ->each(static fn(Model $record) => $record->delete());

        $childComponentContainers = $component->getChildComponentContainers();

        $itemOrder = 1;
        $orderColumn = $component->getOrderColumn();


        foreach ($childComponentContainers as $itemKey => $item) {
            $itemData = $item->getRawState();

            if ($orderColumn) {
                $itemData[$orderColumn] = $itemOrder;
                $itemOrder++;
            }

            if ($record = ($existingRecords[$itemKey] ?? null)) {
                self::updateCustomField($record, $itemData,$customForm);
                continue;
            }

            $record = self::createCustomField($itemData,$customForm);

            $record = $relationship->save($record);
            $item->model($record)->saveRelationships();
        }
    }

    private static function getUsedGeneralFieldIds(array $customFields):array {
        $usedGeneralFields = array_filter(
            array_values($customFields),
            fn($field)=> array_key_exists("general_field_id",$field) && !empty($field["general_field_id"])
        );
        return array_map(fn($used) => $used["general_field_id"],$usedGeneralFields);
    }
}
