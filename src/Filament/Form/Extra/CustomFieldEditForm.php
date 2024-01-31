<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

class CustomFieldEditForm
{

    public static function getCustomFieldSchema(CustomForm $customForm, array $data):array{

        $hasVariations = $customForm->getFormConfiguration()::hasVariations();
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        $type = CustomFormEditForm::getFieldTypeFromRawDate($data);

        $hasOptionsForVariations = $type->canBeRequired()||$type->canBeDeactivate()||$type->hasExtraOptions();
        $showVariations =  $hasVariations  && $type->canHasVariations();
        $columns = $isGeneral||!$hasOptionsForVariations?1:2;


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
                    Toggle::make("has_variations")
                        ->label("Hat Variationen")
                        ->hidden(!$showVariations || !$hasOptionsForVariations)
                        ->columnStart(1)
                        ->live(),

                    Tabs::make()
                        ->columnStart($isGeneral?1:2)
                        ->visible($hasOptionsForVariations)
                        ->tabs(fn (Get $get,$set) => self::getVariationTabs($get, $customForm, $isGeneral, $type, $set)),
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
                    ->schema(self::getCustomFieldVariationRepeaterSchema($type,$isDisabled))
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


    private static function getCustomFieldVariationRepeaterSchema(CustomFieldType $type, bool $isDisabled):array {

        return[

            Section::make()
                ->columns()
                ->schema([
                // Active
                Toggle::make('is_active')
                    ->visible($type->canBeDeactivate())
                    ->label("Aktive") //ToDo Translate
                    ->default(!$isDisabled),

                // Required
                Toggle::make('required')
                    ->visible($type->canBeRequired())
                    ->label("Benötigt") //ToDo Translate
                    ->default(!$isDisabled),
            ]),

            //Type Options
            Group::make()
                ->visible($type->hasExtraOptions())
                ->schema(function (Get $get, $set) use ($type) {
                    $repeater = $type->getExtraOptionsComponent();

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
        ];
    }




    public static function getFieldAddActionSchema():array {
        return [
            //GeneralField
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            //New GeneralFields
            self::getGeneralfieldAddAction(),

            //Space
            Placeholder::make("")->content(""),
            self::getCustomFieldAddActionSchema(),

            //New CustomField
            Placeholder::make("")
                ->label("Spezifische Felder") //ToDo Translate
                ->content(""),

            ];
    }

    private static function getGeneralFieldAddAction():Group {
        return Group::make([
            Select::make("add_general_field_id")
                ->label("")
                ->live()
                ->disableOptionWhen(function($value, Get $get) {
                    return in_array($value, CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")));
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
                self::getFieldAddActions("general_field_id","add_general_field_id","add_general_field")
                    ->disabled(fn(Get $get)=>
                        is_null($get("add_general_field_id")) ||
                        in_array($get("add_general_field_id"), CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")))
                    )
            ]),
        ]);
    }
    public static function getCustomFieldAddActionSchema():Group {
        return Group::make([
            Select::make("add_custom_field_type")
                ->label("")
                ->live()
                ->options(function (CustomForm $record){
                    $formConfiguration = $record->getFormConfiguration();
                    $types = $formConfiguration::formFieldTypes();

                    $keys = array_map(fn($type) => $type::getFieldIdentifier(),$types);
                    $values = array_map(fn($type) => (new $type)->getTranslatedName(), $types);
                    return array_combine($keys,$values);
                }),
            Actions::make([self::getFieldAddActions("type","add_custom_field_type","add_custom_field")]),
        ]);
    }

    private static function getFieldAddActions(string $key, string $getID, string $name): Action {
        return Action::make($name)
            ->modalWidth(fn(Get $get)=> self::getEditCustomFormActionModalWith([$key=> $get($getID)]))
            ->disabled(fn(Get $get)=> is_null($get($getID)))
            ->fillForm(fn($get)=> [$key=> $get($getID)])
            ->closeModalByClickingAway(false)
            ->label(fn()=>"Hinzufügen ") //ToDo Translate
            ->form(fn(Get $get, CustomForm $record)=>
                CustomFieldEditForm::getCustomFieldSchema($record, [$key => $get($getID)])
            )
            ->mutateFormDataUsing(fn(Action $action) =>
                //Get RawSate (yeah is possible)
                array_values($action->getLivewire()->getCachedForms())[1]->getRawState()
            )
            ->action(function ($set,Get $get,array $data) use ($getID) {
                //Add to the other Fields
                $set($getID, null);

                $fields = $get("custom_fields");
                $fields[uniqid()] = $data;
                $set("custom_fields",$fields);
            });
    }


    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditForm::getFieldTypeFromRawDate($state);
        if(!empty($state["general_field_id"])) return 'xl';
        $hasOptionsForVariations = $type->canBeRequired()||$type->canBeDeactivate()||$type->hasExtraOptions();
        if(!$hasOptionsForVariations) return 'xl';
        return'5xl';
    }

    /**
     * @param  Get  $get
     * @param  CustomForm  $customForm
     * @param  bool  $isGeneral
     * @param  CustomFieldType|null  $type
     * @param $set
     * @return array
     */
    private  static function  getVariationTabs(Get $get, CustomForm $customForm, bool $isGeneral, ?CustomFieldType $type, $set): array {
        $tabs = [];

        //If no Variations than skip the Variations and get only the default Tab
        if (!$get("has_variations")) $variationModels = collect([null]);
        else $variationModels = $customForm->variationModelsCached()->prepend(null);

        //VariationTabs
        foreach ($variationModels as $model) {
            if (!is_null($model) && $customForm->getFormConfiguration()::isVariationHidden($model)) continue;

            //Tab Title
            if (is_null($model)) $tabTitle = "Template";//ToDo Translate
            else $tabTitle = $customForm->getFormConfiguration()::variationName($model);

            $isDisabled = is_null($model) ? false : $customForm->getFormConfiguration()::isVariationDisabled($model);

            $varID = $model?->id;

            //Create Tab
            $tabs[] = self::getCustomFieldVariationTab($varID, $isGeneral, $type, $tabTitle, $isDisabled);

            //Set new contend if it is empty
            if (!empty($get("variation-".$varID))) continue;
            $toSet = [
                0 => $type->mutateVariationDataBeforeFill([
                    'is_active' => !$isDisabled,
                    'required' => !$isDisabled,
                ]),
            ];
            $set("variation-".$varID, $toSet);
        }
        return $tabs;
    }

}
