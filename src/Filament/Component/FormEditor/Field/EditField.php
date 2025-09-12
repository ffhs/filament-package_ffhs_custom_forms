<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\Field;


use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
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

    public function getFieldComponents($state, EditCustomForm $livewire): array
    {
        $type = $this->getFieldType($state);
        $localAdding = $livewire->getActiveSchemaLocale() ?? app()->getLocale();
        $actions = $type->getEditorActions($this->getFormConfiguration(), $state);

        return array_merge(
            [
                TextInput::make('name.' . $localAdding)
                    ->visible($type->hasEditorNameElement($state))
                    ->label(CustomField::__('attributes.name.label'))
                    ->helperText(CustomField::__('attributes.name.helper_text')),
                FieldActions::make($actions)
                    ->columnSpan($type->hasEditorNameElement($state) ? 1 : 'full')
                    ->alignEnd(),
            ],
//            $this->getFieldType($state)->getOuterOptions($this->getFormConfiguration(), $state), ToDo May add
            $type->getFieldDataExtraComponents($this->getFormConfiguration(), $state)
        );
    }

    protected function setUp(): void
    {
        $this->schema($this->getFieldComponents(...));
        $this->columns(2);
    }
}
