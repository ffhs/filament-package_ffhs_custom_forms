<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropActions;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;

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

            /* DragDropComponent::make("test1")
                 ->live()

                 ->flatten()
                 ->columnSpanFull()
                 ->nestedFlattenListType(CustomField::class)
                 ->dragDropGroup("testGroup1")
                 ->columns(1)
                 ->flattenGrid(2)
                 ->flattenViewHidden(false)
                 ->itemActions(fn($item) => [
                     Action::make("test")
                         ->icon("bi-input-cursor-text")
                         ->iconButton()
                         ->action(fn($arguments)=> dd($arguments))
                 ])
                ->getFlattenViewLabel('Fields')
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
                //->itemGridSize(2)
                //->itemGridStart(2)

                ->flattenViewHidden(false)
                ->itemIcons('bi-input-cursor-text')
                ->live()
                ->schema([
                   // TextInput::make("wtf1"),
                    //TextInput::make("wtf2"),
                ]),

            DragDropActions::make([
                Action::make("testDropAction")
                    ->action(fn($arguments)=>Debugbar::info($arguments))
            ], Alignment::Right)
                ->dragDropGroup("testGroup1")
                ->fullWidth()
                ->columnSpanFull()->alignment(Alignment::Right),*/


          Group::make([
              /*  Fieldset::make()
                    ->columnStart(1)
                    ->columnSpan(1)
                    ->columns(1)
                    ->schema(fn() =>
                            collect($this->getRecord()->getFormConfiguration()::editorFieldAdder())
                                ->map(fn(string $class) => $class::make($this->getRecord()))
                                ->toArray()
                    ),*/

                EditCustomFields::make("custom_fields")
                    ->columnStart(2)
                    ->columnSpan(5),

            ])
                ->columns(6)
                ->columnSpanFull(),
        ]);
    }
}
