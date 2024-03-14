<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\Extra;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
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
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class CustomFieldEditForm
{

    public static function getCustomFieldSchema(CustomForm $customForm, array $data):array{

        $hasVariations = $customForm->getFormConfiguration()::hasVariations();
        $isGeneral = array_key_exists("general_field_id",$data)&& !empty($data["general_field_id"]);
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
                    Group::make()
                        ->columns(1)
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
                        ]),

                    Tabs::make()
                        ->columnStart($isGeneral?1:2)
                        ->visible($hasOptionsForVariations)
                        ->tabs(fn (Get $get,$set) => self::getVariationTabs($get, $customForm, $isGeneral, $type, $set)),
                ]),
        ];
    }

    private static function getCustomFieldVariationTab(?int $variationId, CustomFieldType $type, String $tabTitle, bool $isDisabled = false): Tab
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
                            ->action(function ($set,Get $get) use ($type, $variationId) {
                                $template = array_values($get("variation-"))[0];
                                $recordName = array_keys($get("variation-".$variationId))[0];
                                $setPrefix = "variation-".$variationId.".".$recordName;

                                $set($setPrefix.'.required', $template['required']);
                                $set($setPrefix.'.is_active', $template['is_active']);

                                //FieldOptions clone
                                $clonedFieldOptions = $type->prepareCloneOptions($template,$setPrefix,$set,$get);
                                $set($setPrefix.'.options', $clonedFieldOptions);
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
                    ->form(fn(Get $get, CustomForm $record) => CustomFieldEditForm::getCustomFieldSchema($record,
                        ["type" => $type::getFieldIdentifier()]))
                    ->modalWidth(fn(Get $get) => self::getEditCustomFormActionModalWith(["type" => $type::getFieldIdentifier()]))
                    ->disabled(fn(Get $get) => is_null($type::getFieldIdentifier()))
                    ->fillForm(fn($get) => ["type" => $type::getFieldIdentifier()])
                    ->closeModalByClickingAway(false)
                    ->action(function ($set, Get $get, array $data) {
                        //Add to the other Fields
                        $fields = $get("custom_fields");
                        $fields[uniqid()] = $data;
                        $set("custom_fields", $fields);
                    })
            ]);
        }
        return $actions;
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
                        ->form(fn(Get $get, CustomForm $record)=> CustomFieldEditForm::getCustomFieldSchema($record, ["general_field_id" => $get("add_general_field_id")]))
                        ->mutateFormDataUsing(fn(Action $action) =>array_values($action->getLivewire()->getCachedForms())[1]->getRawState())//Get RawSate (yeah is possible)
                        ->fillForm(fn($get)=> ["general_field_id"=> $get("add_general_field_id")])
                        ->closeModalByClickingAway(false)
                        ->label(fn()=>"Hinzufügen ") //ToDo Translate
                        ->disabled(fn(Get $get)=>
                           is_null($get("add_general_field_id")) ||
                           collect(CustomFormEditForm::getUsedGeneralFieldIds($get("custom_fields")))
                               ->contains($get("add_general_field_id"))
                        )
                        ->action(function ($set,Get $get,array $data) {
                            //Add to the other Fields
                            $set("add_general_field_id", null);
                            $fields = $get("custom_fields");
                            $fields[uniqid()] = $data;
                            $set("custom_fields",$fields);
                        })
            ]),
        ]);
    }


    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditForm::getFieldTypeFromRawDate($state);
        if(!empty($state["general_field_id"])) return 'xl';
        $hasOptionsForVariations = $type->canBeRequired()||$type->canBeDeactivate()||$type->hasExtraOptions();
        if(!$hasOptionsForVariations) return 'xl';
        return'5xl';
    }


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
            $tabs[] = self::getCustomFieldVariationTab($varID, $type, $tabTitle, $isDisabled);

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
