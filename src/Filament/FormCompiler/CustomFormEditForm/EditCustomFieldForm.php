<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;
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

class EditCustomFieldForm
{


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


    public static function getCustomFieldSchema(array $data, CustomForm $customForm):array{

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

                    EditCustomFieldRule::getRuleComponent($customForm,$type)

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

    public static function mutateOptionDatas(array $data, CustomForm $customForm): array {
        if(!array_key_exists("options",$data) || is_null($data["options"])) $data["options"] = [];

        $type = CustomFormEditForm::getFieldTypeFromRawDate($data);
        $field = $customForm->customFields->where("id",$data["id"])->first();
        if($field == null) return $data;


        foreach ($type->getExtraTypeOptions() as $name => $option){
            /**@var TypeOption $option*/
            if(!array_key_exists($name, $data["options"])) $data["options"][$name] = $option->mutateOnLoad(null, $field);
            else $data["options"][$name] = $option->mutateOnLoad($data["options"][$name],$field);
        }

        return $data;
    }


}
