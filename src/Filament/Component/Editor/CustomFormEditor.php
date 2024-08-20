<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\RuleEditor\RuleEditor;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;

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
        $this->columns(6);

        $this->schema([
            RuleEditor::make()
                ->targets(function ($get,$record){
                    Debugbar::info($get("custom_fields"));
                    return collect($get("custom_fields"))->pluck("name.". $record->getLocale(), "identifier"); //ToDo Improve
                })
                ->columnSpanFull(),
            Fieldset::make()
                ->columnStart(1)
                ->columnSpan(1)
                ->columns(1)
                ->schema(fn() =>
                   collect($this->getRecord()->getFormConfiguration()::editorFieldAdder())
                       ->map(fn(string $class) => $class::make())
                       ->toArray()
                ),

            EditCustomFields::make("custom_fields")
                ->columnStart(2)
                ->columnSpan(5),

        ]);
    }
}
