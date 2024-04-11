<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;

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
                    EditorCustomFieldList::make($record)
                        ->saveRelationshipsUsing(fn($component, $state) => CustomFormEditorSaveHelper::saveCustomFields($component,$record,$state))

                        //If it is a template it haven't to Check the fields
                        ->rules($record->is_template?[]:[CustomFormEditorSaveHelper::getGeneralFieldRepeaterValidationRule()]),
                ]),
        ]);
    }





}
