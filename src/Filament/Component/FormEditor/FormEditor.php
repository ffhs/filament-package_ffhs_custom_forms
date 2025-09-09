<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FormEditorSideComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field\EditFieldsGroup;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->columnSpanFull();
        $this->columns(5);
        $this->schema([
            Fieldset::make()
                ->columnSpan(1)
                ->columns(1)
                ->schema($this->getSideComponents(...)),
            EditFieldsGroup::make('custom_fields')
                ->childComponentSizeUsing($this->getFieldGridSize(...))
                ->columnSpan(4)
                ->formConfiguration($this->getFormConfiguration(...))
        ]);
    }

    protected function getFieldGridSize(array $state, string $value): int
    {
        $itemState = $state[$value] ?? [];
        $size = $itemState['options']['column_span'] ?? null;
        $maxSize = 12;

        if (!empty($size)) {
            return min($size, $maxSize);
        }

        $type = CustomForms::getFieldTypeFromRawDate($itemState, $this->getFormConfiguration());

        return $type->isFullSizeField() ? $maxSize : 1;
    }
}
