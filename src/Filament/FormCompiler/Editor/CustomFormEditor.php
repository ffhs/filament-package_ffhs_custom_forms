<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFormSave;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CustomFormEditor extends Component
{

    protected string $view = 'filament-forms::components.group';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }


    protected function setUp(): void {
        parent::setUp();
        $this->label("");

        $this->columnSpanFull();
        $this->columns(3);

        $this->schema([
            //Field Adder
            Fieldset::make()
                ->columnStart(1)
                ->columnSpan(1)
                ->columns(1)
                ->schema(function(CustomForm $record){
                    $record->getFormConfiguration()::editorFieldAdder();

                    return collect($record->getFormConfiguration()::editorFieldAdder())
                        ->map(fn (string $class) => $class::make($record))->toArray();
                }),

            //Fields Overview
            Group::make()
                ->columns(1)
                ->columnSpan(2)
                ->schema(fn(CustomForm $record)=>[
                    self::getCustomFieldRepeater($record)
                        ->saveRelationshipsUsing(fn($component, $state) =>EditCustomFormSave::saveCustomFields($component,$record,$state))
                        //If it is a template it haven't to Check the fields
                        ->rules($record->is_template?[]:[EditCustomFormSave::getGeneralFieldRepeaterValidationRule()]),
                ]),
        ]);
    }


    private static function getCustomFieldRepeater($record): Repeater {

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
            $repeaterActions[] = $action->getAction($record,$closures);
        }

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
            ->extraItemActions($repeaterActions)
            ->itemLabel(function($state){
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

                //flex Body
                $html=  '<span  class="cursor-pointer flex">'.$html . '</span>';

                //Close existing heading and after that reopen it
                $html=  '</h4>'.$html . '<h4>';
                return  new HtmlString($html);
            }
            )

            ->schema([
                Group::make()
                    ->visible(fn($state)=>CustomFormEditorHelper::getFieldTypeFromRawDate($state) instanceof CustomFieldType)
                    ->schema(function(Get $get,$state) use ($record) {
                        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
                        if($type instanceof CustomLayoutType)
                            return[self::getCustomFieldRepeater($record)];
                        else return [];
                    }),
            ]);
    }

}
