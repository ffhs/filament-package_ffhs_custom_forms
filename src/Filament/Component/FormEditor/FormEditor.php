<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FormEditorSideComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\EditFieldsGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormGroupName;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Fieldset;

class FormEditor extends Field
{
    use HasFormConfiguration;
    use HasFormGroupName;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.form-editor.index';

    public function getSideComponents(): array
    {
        $components = [];
        $classes = $this->getFormConfiguration()->getSideComponentModifiers();
        $formConfiguration = $this->getFormConfiguration();

        foreach ($classes as $class) {
            /**@var FormEditorSideComponent $class */
            $components[] = $class::getSiteComponent($formConfiguration);
        }

        return $components;
    }

    public function getDefaultStateCasts(): array
    {
        $casts = parent::getDefaultStateCasts();
        $casts[] = new CustomFieldStateCast();

        return $casts;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull();
        $this->columns(5);
        $this->schema([
            Fieldset::make()
                ->schema($this->getSideComponents(...))
                ->columnSpan(1)
                ->columns(1),
            EditFieldsGroup::make('custom_fields')
                ->columns(fn() => $this->getFormConfiguration()->getColumns())
                ->formConfiguration($this->getFormConfiguration(...))
                ->columnSpan(4),
        ]);
    }


}
