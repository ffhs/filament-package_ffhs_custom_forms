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
                return EditCustomFieldRule::mutateRuleDataOnLoad($data, $record);
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
                EditCustomFieldAction::getTemplateDissolveAction($record),
                //ToDo make that the actions can be set in the FieldType's
            ])
            ->itemLabel(function($state){
                $type = EditCustomFormFieldFunctions::getFieldTypeFromRawDate($state);

                //Before Icon
                $html = $type->editModeNameBeforeIcon($state);

                //Prepare the Icon
                $icon = Blade::render('<x-'. $type->icon() .' class="h-4 w-4"/>');
                $icon = '<span class="px-2 py-1"> ' .$icon . '</span>';
                $html.= $icon;

                //Name
                $nameStyle = 'class="text-sm font-medium ext-gray-950 dark:text-white truncate select-none"';
                $name = $type->editModeName($state);
                $html.= '<h4'.$nameStyle.'>' . $name . '</h4>';

                //flex Body
                $html=  '<span  class="cursor-pointer flex">'.$html . '</span>';

                //Close existing heading and after that reopen it
                $html=  '</h4>'.$html . '<h4>';
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
