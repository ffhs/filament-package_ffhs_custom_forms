<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorMutationHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

final class EditorCustomFieldList extends Repeater
{
    use UseComponentInjection;

    protected CustomForm $form;

    public static function make(CustomForm|string $name): static {
        return self::injectIt($name, ['name' => "custom_fields"]);
    }


    protected function setUp(): void {
        $this->form = $this->injection;
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
            $repeaterActions[] = $action->getAction($this->injection,$closures);
        }

        $this->collapseAllAction(fn(Action $action)=> $action->hidden())
            ->label("")
            ->orderColumn("form_position")
            ->relationship("customFieldInLayout")
            ->collapsible(false)
            ->extraItemActions($repeaterActions)
            ->addable(false)
            ->defaultItems(0)
            ->columnSpan(2)
            ->persistCollapsed()
            ->reorderable()
            ->lazy()
            ->saveRelationshipsUsing(fn()=>empty(null))
            ->expandAllAction(fn(Action $action)=> $action->hidden())
            ->collapsed()
            ->itemLabel(fn(array $state)=> CustomFormEditorHelper::getFieldTypeFromRawDate($state)->getEditorItemTitle($state,$this->form))
            ->mutateRelationshipDataBeforeFillUsing(function($data){
                $data = CustomFormEditorMutationHelper::mutateOptionData($data, $this->injection);
                return CustomFormEditorMutationHelper::mutateRuleDataOnLoad($data, $this->injection);
            })
            ->schema([
                Group::make(function(Get $get,$state) {
                    $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
                    $contend = $type->editorRepeaterContent($this->injection,$state);
                    return empty($contend)?[]:$contend;
                }),
            ]);

    }


}
