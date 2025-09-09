<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;


use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFormConfiguration;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class EditField extends Component
{
    use HasFormConfiguration;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.form-editor.field';

    public static function make()
    {
        /**@var EditField $static */
        $static = app(static::class);
        $static->configure();
        return $static;
    }

    public function getFieldType($state): CustomFieldType
    {
        return CustomForms::getFieldTypeFromRawDate($state, $this->getFormConfiguration());
    }

    public function getFieldComponents($state): array
    {
        $type = $this->getFieldType($state);

        return array_merge(
            [
                TextInput::make('name'),
                FieldActions::make($type->getEditorActions($this->getFormConfiguration(), $state))->alignEnd(),
            ],
//            $this->getFieldType($state)->getOuterOptions($this->getFormConfiguration(), $state), ToDo Maby add
            $type->getFieldDataExtraComponents($this->getFormConfiguration(), $state)
        );
    }

    protected function setUp(): void
    {
        $this->schema($this->getFieldComponents(...));
        $this->columns(2);
    }
}
