<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Ffhs\FfhsUtils\Filament\DragDrop\DragDropGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder\FormFieldTypeAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Fieldset;

class FormEditor extends Field
{
    use HasFormConfiguration;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.form-editor.index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull();
        $this->columns(5);
        $this->schema([
            Fieldset::make()
                ->columnSpan(1)
                ->columns(1)
                ->schema([
                    FormFieldTypeAdder::make('add')
                        ->formConfiguration($this->getFormConfiguration(...))
                ]),
            DragDropGroup::make('custom_fields')
                ->columns(fn() => $this->getFormConfiguration()->getColumns())
                ->columnSpan(4)
                ->hiddenLabel()
        ]);
    }
}
