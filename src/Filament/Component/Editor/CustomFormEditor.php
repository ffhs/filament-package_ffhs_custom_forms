<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

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
