<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormEditorValidation\FormEditorValidation;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldList\EditorCustomFieldList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
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

            Group::make([
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
                ->columnSpanFull(),
        ]);
    }
}
