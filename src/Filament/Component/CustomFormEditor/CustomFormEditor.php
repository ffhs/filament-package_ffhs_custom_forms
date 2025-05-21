<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\EditCustomFields;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class CustomFormEditor extends Field {
    protected string $view = 'filament-forms::components.group';

    protected function setUp(): void {
        parent::setUp();
        $this->columnSpanFull()
            ->label("");

        $this->schema([
            Tabs::make()
                ->extraAttributes(["class" => "overflow-y-auto scroll-smooth"])
                ->columnSpanFull()
                ->tabs([
                    Tab::make("Formular") //ToDo Translate
                        ->icon("carbon-data-format")
                        ->columns(6)
                        ->schema([
                            Fieldset::make()
                                ->columnStart(1)
                                ->columnSpan(1)
                                ->columns(1)
                                ->schema(fn() => //ToDo Remove Closure
                                $this->getRecord()?
                                    collect($this->getRecord()->getFormConfiguration()::editorFieldAdder())
                                            ->map(fn(string $class) => $class::make())
                                            ->toArray(): []
                                ),
                            EditCustomFields::make("custom_fields")
                                ->columnStart(2)
                                ->columnSpan(5),

                        ]),

                    Tab::make("Regeln") //ToDo Translate
                        ->icon("carbon-rule-draft")
                        ->schema([
                            RuleEditor::make()
                                ->columnSpanFull()
                        ])
                ]),

        ]);
    }
}
