<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragAndDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
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

        $this->schema([

             DragDropComponent::make("test")
                ->formatStateUsing(fn() => [
                    "test1" => [
                        "wtf1" => "test",
                        "wtf2" => "test",
                    ],
                    "test2" => [
                        "wtf1" => "test2",
                        "wtf2" => "test2",
                    ]
                ])
                ->columnSpanFull()
                 ->nestedFlattenListType(CustomField::class)
                 ->dragDropGroup("testGroup1")
                ->columns(1)
                 ->itemActions(fn($item) => [Action::make($item. "-test")->action(fn()=> dd())])
                ->gridSize(4)
                ->schema([
                    TextInput::make("wtf1"),
                    TextInput::make("wtf2"),
                ]),

            DragDropComponent::make("test2")
                ->formatStateUsing(fn() => [
                    "test3" => [
                        "wtf3" => "test3",
                        "wtf4" => "test3",
                    ],
                    "test4" => [
                        "wtf5" => "test3",
                        "wtf6" => "test3",
                    ]
                ])
                ->columnSpanFull()
                ->dragDropGroup("testGroup1")
                ->columns(1)
                ->gridSize(4)
                ->schema([
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
