<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

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



    private static function getFieldOptionSection(CustomFieldType $type): Section {
        return Section::make("Optionen") //ToDo Translate
            ->schema([


            ]);
    }

    public static function mutateOptionData(array $data, CustomForm $customForm): array {
        if(!array_key_exists("options",$data) || is_null($data["options"])) $data["options"] = [];

        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($data);
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
