<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFormSave;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\HtmlComponents\HtmlBadge;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CustomFormEditForm
{
    public static function formSchema(): array {
        return [
            Fieldset::make()
                ->columnStart(1)
                ->columnSpan(1)
                ->columns(1)
                ->schema(fn(CustomForm $record)=>EditCustomFieldAction::getFieldAddActionSchema($record)),

            Group::make()
                ->columns(1)
                ->columnSpan(2)
                ->schema(fn(CustomForm $record)=>[
                    self::getCustomFieldRepeater($record)
                        ->saveRelationshipsUsing(fn($component, $state) =>EditCustomFormSave::saveCustomFields($component,$record,$state))
                        //If it is a template it haven't to Check the fields
                        ->rules($record->is_template?[]:[EditCustomFormSave::getGeneralFieldRepeaterValidationRule()]),
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
            ->mutateRelationshipDataBeforeFillUsing(function($data) use ($record) {
                $data = EditCustomFieldForm::mutateOptionDatas($data, $record);
                return EditCustomFieldRule::mutateRuleDatasOnLoad($data, $record);
            })
            ->collapsible(false)
            ->addable(false)
            ->defaultItems(0)
            ->columnSpan(2)
            ->persistCollapsed()
            ->label("")
            ->reorderable()
            ->collapsed()
            ->lazy()
            ->extraItemActions([
                EditCustomFieldAction::getPullOutLayoutAction(),
                EditCustomFieldAction::getPullInLayoutAction(),
                EditCustomFieldAction::getEditCustomFieldAction($record),
                EditCustomFieldAction::getTemplateDissolveAction(),
            ])
            ->itemLabel(function($state){
                $styleClasses = "text-sm font-medium ext-gray-950 dark:text-white truncate select-none";
                $type = self::getFieldTypeFromRawDate($state);
                $generalBadge= null;
                $templateBadge = null;

                if(!empty($state["general_field_id"])){
                    $generalBadge = new HtmlBadge("Gen", Color::rgb("rgb(43, 164, 204)")); Blade::render('<x-filament::badge size="Gen">New</x-filament::badge>');
                    $genField = GeneralField::cached($state["general_field_id"]);
                    $name = $genField->name_de; //ToDo Translate
                    $icon = Blade::render('<x-'. $genField->icon .' class="h-4 w-4 "/>') ;
                }
                else if(!empty($state["template_id"])){
                    $templateBadge = new HtmlBadge("Template", Color::rgb("rgb(34, 135, 0)")); Blade::render('<x-filament::badge size="Gen">New</x-filament::badge>');
                    $template = CustomForm::cached($state["template_id"]);
                    $name = $template->short_title;
                    $icon = Blade::render('<x-'. $type->icon() .' class="h-4 w-4 "/>') ;
                }
                else  {
                    $name = $state["name_de"]; //ToDo Translate
                    $icon = Blade::render('<x-'. $type->icon() .' class="h-4 w-4 "/>') ;
                }

                $badgeCount =null;
                if($type instanceof CustomLayoutType){
                    $size = empty($state["custom_fields"])?0:sizeof($state["custom_fields"]);
                    $badgeCount = new HtmlBadge($size);
                    $span = '<span x-on:click.stop="isCollapsed = !isCollapsed" class="cursor-pointer flex" >';
                }
                else $span = '<span  class="cursor-pointer flex">';

                $h4 = '<h4 class="'.$styleClasses.'">';
                $html = "</h4>". $span;
                if(!is_null($badgeCount)) $html .= '<span class="px-1.5">'. $badgeCount. '</span>';
                if(!is_null($generalBadge)) $html .= '<span class="px-1.5">'. $generalBadge. '</span>';
                if(!is_null($templateBadge)) $html .= '<span class="px-1.5">'. $templateBadge. '</span>';
                $html .= '<span class="px-1.5">' .$icon . '</span>'. $h4 . $name . " </h4></span><h4>";

                 return  new HtmlString($html);
               }
            )

            ->schema([
                Group::make()
                    ->visible(fn($state)=>self::getFieldTypeFromRawDate($state) instanceof CustomFieldType)
                    ->schema(function(Get $get,$state) use ($record) {
                        $type = self::getFieldTypeFromRawDate($state);
                        if($type instanceof CustomLayoutType)
                            return[self::getCustomFieldRepeater($record)];
                        else return [];
                    }),
            ]);
    }


    public static function getFieldTypeFromRawDate(array $data): ?CustomFieldType {
        $isGeneral = array_key_exists("general_field_id",$data)&& !is_null($data["general_field_id"]);
        $isTemplate = array_key_exists("template_id",$data)&& !is_null($data["template_id"]);
        if($isTemplate) return new TemplateFieldType();
        return $isGeneral? GeneralField::cached($data["general_field_id"])->getType(): CustomFieldType::getTypeFromName($data["type"]);
    }

    public static function getUsedGeneralFieldIds(array $customFields):array {

        //GeneralFieldIds From GeneralFields
        $generalFields = self::getFieldsWithProperty($customFields,"general_field_id");
        $generalFieldId = array_map(fn($used) => $used["general_field_id"],$generalFields);


        //GeneralFieldIds From Templates
        $templateData = self::getFieldsWithProperty($customFields,"template_id");
        $templateIds = array_map(fn($used) => $used["template_id"],$templateData);
        foreach ($templateIds as $templateId){
            $genFields = CustomForm::cached($templateId)?->generalFields->pluck("id")->toArray();
            $generalFieldId = array_merge($generalFieldId,$genFields);
        }

        return $generalFieldId;
    }




    private static function getFieldsWithProperty (array $customFields, string $property):array  {
        $foundFields = array_filter(
            array_values($customFields),
            fn($field)=> !empty($field[$property])
        );
        $nestedFields = collect(array_values($customFields))
            ->filter(fn($field)=> !empty($field["custom_fields"]))
            ->map(fn($field)=> $field["custom_fields"]);


        $foundFields=  array_filter($foundFields, fn($value)=> !is_null($value));

        if($nestedFields->count() > 0){
            $foundNestedFields = $nestedFields
                ->map(fn(array $fields)=> self::getUsedGeneralFieldIds($fields))
                ->flatten(1);
            return array_merge($foundFields, $foundNestedFields->toArray());
        }

        return $foundFields;
    }


}
