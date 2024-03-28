<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFormFieldFunctions;
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
                $data = EditCustomFieldForm::mutateOptionData($data, $record);
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
                $type = EditCustomFormFieldFunctions::getFieldTypeFromRawDate($state);
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
                    ->visible(fn($state)=>EditCustomFormFieldFunctions::getFieldTypeFromRawDate($state) instanceof CustomFieldType)
                    ->schema(function(Get $get,$state) use ($record) {
                        $type = EditCustomFormFieldFunctions::getFieldTypeFromRawDate($state);
                        if($type instanceof CustomLayoutType)
                            return[self::getCustomFieldRepeater($record)];
                        else return [];
                    }),
            ]);
    }





}
