<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList;

use ErrorException;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use RuntimeException;

final class EditorCustomFieldList extends Repeater
{

    protected CustomForm $form ;

    public static function make(CustomForm|string $name): static {
        if(!($name instanceof CustomForm)) throw  new RuntimeException("pls use a CustomForm and not a string");
        $static = app(static::class, ['name' => "custom_fields", 'form'=>"dd"]);
        /**@var EditorCustomFieldList $static*/
        $static->setForm($name);
        $static->configure();
        return $static;
    }


    protected function setUp(): void {
        parent::setUp();
        $repeaterActionClasses = [];

        foreach (CustomFieldType::getAllTypes() as $typeClass){
            /**@var CustomFieldType $type*/
            $type = new $typeClass();
            foreach ($type->repeaterFunctions() as $actionClass => $function){
                if(empty($repeaterActionClasses[$actionClass])) $repeaterActionClasses[$actionClass] = [];
                $repeaterActionClasses[$actionClass][] = $function;
            }
        }

        $repeaterActions = [];
        foreach ($repeaterActionClasses as $actionClass => $closures){
            /**@var RepeaterFieldAction $action*/
            $action = new $actionClass();
            $repeaterActions[] = $action->getAction($this->form,$closures);
        }

        $this->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->relationship("customFieldInLayout")
            ->orderColumn("form_position")
            ->saveRelationshipsUsing(fn()=>empty(null))
            ->mutateRelationshipDataBeforeFillUsing(function($data){
                $data = EditCustomFieldForm::mutateOptionData($data, $this->form);
                return EditCustomFieldRule::mutateRuleDataOnLoad($data, $this->form);
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
            ->extraItemActions($repeaterActions)
            ->itemLabel(fn(array $state)=> $this->getFieldRepeaterItemLabel($state))
            ->schema([
                Group::make(function(Get $get,$state) {
                    $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
                    $contend = $type->editorRepeaterContent($this->form,$state);
                    return empty($contend)?[]:$contend;
                }),
            ]);

    }

    private function getFieldRepeaterItemLabel(array $state): mixed {
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);

        //Before Icon
        $html = $type->nameBeforeIconFormEditor($state);

        //Prepare the Icon
        $icon = Blade::render('<x-'. $type->icon() .' class="h-4 w-4"/>');
        $icon = '<span class="px-2 py-1"> ' .$icon . '</span>';
        $html.= $icon;

        //Name
        $nameStyle = 'class="text-sm font-medium ext-gray-950 dark:text-white truncate select-none"';
        $name = $type->nameFormEditor($state);
        $html.= '<h4'.$nameStyle.'>' . $name . '</h4>';

        //Do Open the Record if possible
        $clickAction = '';
        if(!empty($type->editorRepeaterContent($this->form,$state)))
            $clickAction= 'x-on:click.stop="isCollapsed = !isCollapsed"';


        $html= '<span  class="cursor-pointer flex"'.$clickAction.'>' . $html . '</span>';

        //Close existing heading and after that reopen it
        $html=  '</h4>'.$html . '<h4>';
        return  new HtmlString($html);
    }

    private function setForm($form):void {
        $this->form = $form;
    }


}
