<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder\CustomFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DefaultEditorComponents\FieldAdder\GeneralFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;

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

        $this->schema([
            /*
                        DragDropComponent::make("test1")
                            ->columnSpanFull()
                            ->flatten()
                            ->nestedFlattenListType(CustomField::class)


                            ->dragDropGroup("testGroup1")
                            ->columns(1)

                            ->flattenGrid(2)
                            ->flattenViewLabel('Fields')
                            ->flattenGrid(1)

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
                            ->columnSpanFull()->alignment(Alignment::Right),*//**/


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

              Fieldset::make()
                 ->columnStart(1)
                 ->columnSpan(1)
                 ->columns(1)
                 ->schema([
                    CustomFieldTypeAdder::make(),
                    GeneralFieldAdder::make()
                    /*DragDropExpandActions::make()#
                        ->action(fn()=>
                            Action::make("test")
                                ->action(fn()=>dd())
                        )
                        ->disableOptionWhen(fn($value) => $value == "test4")
                     ->label("general Fields")
                        ->dragDropGroup('custom_fields')
                     ->options([
                         "test1" => "test1",
                         "test2" => "test2",
                         "test3" => "test3",
                         "test4" => "test4",
                     ])*/
                 ]),


              EditCustomFields::make("custom_fields")
                    ->columnStart(2)
                    ->columnSpan(5),

            ])
                ->columns(6)
                ->columnSpanFull(),
        ]);
    }
}
