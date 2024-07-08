<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class CustomFormEditor extends Component {

    protected string $view = 'filament-forms::components.group';

    public static function make(): static {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->columnSpanFull();
        $this->columns(3);
        $this->afterStateUpdated(fn($state) => Debugbar::info($state));


        $this->schema([

             DragDropComponent::make("test1")
                 ->live()

                 ->flatten()
                 ->columnSpanFull()
                 ->nestedFlattenListType(CustomField::class)
                 ->dragDropGroup("testGroup1")
                 ->columns(1)
                 ->flattenViewHidden(false)
                 ->itemActions(fn($item) => [
                     Action::make("test")
                         ->icon("bi-input-cursor-text")
                         ->iconButton()
                         ->action(fn($arguments)=> dd($arguments))
                 ])
                ->gridSize(2)
                ->schema([
                    TextInput::make("wtf1"),
                    TextInput::make("wtf2"),


                    DragDropComponent::make("subTest1")
                        ->dragDropGroup("testGroup1")
                        //->dragDropGroup("testGroup2")
                        ->live()
                        ->orderAttribute('pos')
                        ->schema([
                            TextInput::make("wtf1"),
                        ])
                ]),



            DragDropComponent::make("test2")
                ->columnSpanFull()
                ->orderAttribute("pos")
                ->nestedFlattenListType(CustomField::class)
                ->dragDropGroup("testGroup1")
                ->columns(1)
                ->gridSize(4)
                ->flattenViewHidden(false)
                ->itemIcons('bi-input-cursor-text')
                ->live()
                ->schema([
                   // TextInput::make("wtf1"),
                    //TextInput::make("wtf2"),
                ]),


          /* Group::make([
                Fieldset::make()
                    ->columnStart(1)
                    ->columnSpan(1)
                    ->columns(1)
                    ->schema(fn() =>
                            collect($this->getRecord()->getFormConfiguration()::editorFieldAdder())
                                ->map(fn(string $class) => $class::make($this->getRecord()))
                                ->toArray()
                    ),

                EditCustomFormFields::make("custom_fields")
                    ->columnStart(2)
                    ->columnSpan(5),

            ])
                ->columns(6)
                ->columnSpanFull(),*/
        ]);
    }
}
