<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
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
use Filament\Forms\Form;
use Filament\Support\Colors\Color;

trait FormForm
{
    public function form(Form $form): Form {
        return $form
            ->schema([

                Section::make("Form")
                    ->columns(3)
                    ->schema([
                        Fieldset::make()
                            ->columnStart(1)
                            ->columnSpan(1)
                            ->columns(1)
                            ->schema($this->getFieldAddSchema()),

                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Repeater::make("custom_fields")
                                    ->itemLabel(fn($state)=>array_key_exists("general_field_id",$state)? GeneralField::cached($state["general_field_id"])->name_de :$state["name_de"]) //toDo Translate
                                    ->reorderableWithDragAndDrop(false)
                                    ->relationship("customFields")
                                    ->addable(false)
                                    ->reorderableWithButtons()
                                    ->defaultItems(0)
                                    ->persistCollapsed()
                                    ->reorderable()
                                    ->collapsed()
                                    ->expandAllAction(fn(Action $action)=> $action->hidden())
                                    ->collapseAllAction(fn(Action $action)=> $action->hidden())
                                    ->schema([TextInput::make("create_name")->live()->afterStateUpdated(fn( ?string $old, $set)=> $set("create_name",$old))])
                                    ->extraItemActions([
                                        Action::make('edit')
                                            ->icon('heroicon-m-pencil-square')
                                            ->fillForm(fn($state,$arguments) => ["customFields"=> [$state[$arguments["item"]]]])
                                            ->form(fn($get,$state,$arguments)=> self::getCustomFieldForm($get("custom_form_identifier"), $state[$arguments["item"]]))
                                            ->mutateFormDataUsing(fn($data,$get) => array_merge($data["customFields"][0], ["type" => $get("add_custom_field_type")]))
                                            ->action(function ($state ): void {
                                                dd($state); //ToDo Save changes
                                            }),
                                    ]),

                            ]),
                    ]),

            ]);
    }

    private function getFieldAddSchema():array {
        return [
            //GeneralField
            Placeholder::make("")
                ->label("Generelle Felder") //ToDo Translate
                ->content(""),

            Select::make("add_general_field_id")
                ->label("")
                ->live()
                ->options(function ($get){
                    $formIdentifier = $get("custom_form_identifier");
                    $generalFields= GeneralFieldForm::query()
                        ->where("custom_form_identifier", $formIdentifier)
                        ->with("generalField")->get()
                        ->map(fn($generalFieldForm)=>$generalFieldForm->generalField);
                    return $generalFields->pluck("name_de","id"); //ToDo Translate
                }),

            Actions::make([
                Action::make("add_general_field")
                    ->mutateFormDataUsing(fn($data,$get) => array_merge($data["customFields"][0], ["general_field_id" => $get("add_general_field_id")]))
                    ->disabled(fn($get)=>is_null($get("add_general_field_id")))
                    ->label("Hinzufügen") //ToDo Translate
                    ->fillForm(["customFields"=>["new"=>[]]])
                    ->form(fn($get)=>self::getCustomFieldForm($get("custom_form_identifier"), ["general_field_id" => $get("add_general_field_id")]))

                    ->action(function ($set,$get,array $data){
                        $customFieldData = array_filter($data, fn($key) => !str_starts_with($key, 'variation-'), ARRAY_FILTER_USE_KEY);
                        dd($data,$customFieldData);
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
                    $formConfiguration = self::getFormConfiguration($formIdentifier);
                    $types = $formConfiguration::formFieldTypes();

                    $keys = array_map(fn($type) => $type::getFieldIdentifier(),$types);
                    $values = array_map(fn($type) => (new $type)->getTranslatedName(), $types);
                    return array_combine($keys,$values);
                }),
            Actions::make([
                Action::make("add_custom_field")
                    ->mutateFormDataUsing(fn($data,$get) => array_merge($data["customFields"][0], ["type" => $get("add_custom_field_type")]))
                    ->disabled(fn($get)=>is_null($get("add_custom_field_type")))
                    ->label("Hinzufügen") //ToDo Translate
                    ->fillForm(["customFields"=>["new"=>[]]])
                    ->form(fn($get)=>
                        self::getCustomFieldForm($get("custom_form_identifier"), ["type" => $get("add_custom_field_type")])
                    )
                    ->action(function ($set,$get,array $data){
                        $fields = $get("custom_fields");
                        $id = uniqid();
                        $data["create_name"] = $id;
                        $fields[$id] = $data;
                        $set("custom_fields",$fields);
                    }),
            ])];


    }
    private static function getFormConfiguration($formIdentifier): string{
        return collect(config("ffhs_custom_forms.forms"))->where(fn(string $class)=> $class::identifier() == $formIdentifier)->first();
    }


    private static function getCustomFieldForm(string $formIdentifyer, array $data):array{

        $hasVariations = self::getFormConfiguration($formIdentifyer)::hasVariations();
        $isGeneral = array_key_exists("general_field_id", $data);
        //$isNew = !array_key_exists("id", $data);
        $type = $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);

        return [
            Repeater::make("customFields")
                ->reorderable(false)
                ->deletable(false)
                ->addable(false)
                ->defaultItems(0)
                ->label("")
                ->columns(2)
                ->schema([

                    Group::make()
                        ->extraAttributes(['style' => "background-color: '#FFE5E5'"])
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
                                ->tabs(function ($get) use ($type, $isGeneral) {
                                    $tabs = [];

                                    $tabs[] = self::createCustomFieldVariationTab(null, $isGeneral, $type);
                                    if(!$get("has_variations")) return $tabs;

                                    return $tabs;
                                })
                        ])
                ])
        ];
    }


    private static function createCustomFieldVariationTab(?int $variationId, bool $isGeneral, CustomFieldType $type): Tabs\Tab
    {

        $isTemplate = is_null($variationId);

        $tabTitle = "Template";
        if (!is_null($variationId)) {
            $tabTitle = "Variation " . $variationId; //ToDo title from DynamicFormConfiguration
        }

        return  Tabs\Tab::make($tabTitle)
            ->schema([
                Repeater::make("variation-".$variationId)
                    ->reorderable(false)
                    ->deletable(false)
                    ->cloneable(true)
                    ->addable(false)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->label("")
                    ->live()
                    ->schema([
                        self::getCustomFieldVariationRepeaterSchema($isGeneral, $type)
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(fn(array $data)=>$type->prepareOptionDataBeforeCreate($data))
                    /*ToDo
                       // Disabled active if they new to a disabled term
                       if($isTermDisabled)$data["is_active"] = false;
                     */
                    ->mutateRelationshipDataBeforeFillUsing(fn (array $data)=>$type->prepareOptionDataBeforeFill($data))
                    ->mutateRelationshipDataBeforeSaveUsing(fn ($data) =>$type->prepareOptionDataBeforeSave($data))
                    //Create new Recorde if a new ProductTerm add
                    ->hidden(function (array|null $state, Repeater $component,$set)  {
                        if (!empty($state)) return;
                        $newRecord = [uniqid() => ['is_active' => true, 'required' => true, 'field_options'=>['options'=>[]]]];
                        $set($component->getName(), $newRecord);
                    })
                    ->cloneAction(
                        fn(Action $action) => $action
                            ->color(fn() => $isTemplate? Color::Zinc: Color::Orange)
                            ->disabled($isTemplate )
                            ->label("Vom Template")
                            ->action(function ($set,$get) use ($type, $variationId, $isGeneral) {
                                $template = array_values($get("variation-"))[0];
                                $recordName = array_keys($get("variation-".$variationId))[0];
                                $setPrefix = "variation-".$variationId.".".$recordName.".";

                                $set($setPrefix.'required', $template['required']);
                                $set($setPrefix.'is_active', $template['is_active']);

                                //FieldOptions clone
                                $clonedFieldOptions = $type->prepareCloneOptions($template['field_options'],$isGeneral);
                                $set($setPrefix.'field_options', $clonedFieldOptions);
                            })
                    )
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


    private static function getCustomFieldVariationRepeaterSchema(bool $isGeneral, CustomFieldType $type):Group {

        return Group::make([

            //Setings
            Group::make()
                ->columnStart(1)
                ->schema([

                    Section::make()
                        ->schema([
                            // Active
                            Toggle::make('is_active')
                                ->label("Aktive") //ToDo Translate
                                ->default(true),

                            // Required
                            Toggle::make('required')
                                ->label("Benötigt") //ToDo Translate
                                ->default(true),
                        ])->columns(2),


                    //TypeOptions
                    Group::make()
                        ->schema(function ($get, $set) use ($type, $isGeneral) {
                            $repeater = $type->getExtraOptionsRepeater();

                            if(is_null($repeater)) return [];
                            //Need for to Add to begin of a new one
                            $fieldOptions = $get("options");
                            if(is_null($fieldOptions) || empty($fieldOptions) || (sizeof($fieldOptions) == 1 && array_key_exists("options",$fieldOptions) && empty($fieldOptions["options"]) )) {
                                $set("options", ["options" => $type->getExtraOptionFields()]);
                            }
                            return [$repeater];
                        })
                        ->visible($type->hasExtraOptions()),
                ])
                ->columns(1),
        ]);
    }
}
