<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;

trait HasTriggerEventFormTargets
{
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;
    use CanLoadFieldRelationFromForm;

    protected Collection|null $cachedAllFieldsData = null;

    public function getTargetsSelect(): Select
    {
        return Select::make('targets')
            ->multiple()
            ->label('Target')
            ->options($this->getTargetOptions(...))
            ->lazy()
            ->hidden(function ($set, $get) {
                //Fields with an array doesn't generate properly
                if (is_null($get('targets'))) {
                    $set('targets', []);
                }
            })
            ->live();
    }

    public function getTargetSelect(): Select
    {
        return Select::make('target')
            ->options($this->getTargetOptions(...))
            ->label('Target')
            ->live();
    }

    public function getTargetOptions(Get $get): array
    {
        $formConfiguration = $this->getFormConfiguration($get);
        $fields = collect($this->getAllFieldsData($get, $formConfiguration))
            ->filter(fn(EmbedCustomField $field) => is_null($field->template_id));

        return $this->getSelectOptionsFromFields($fields, $get);
    }

    public function getAllFieldsData(Get $get, CustomFormConfiguration $formConfiguration): Collection
    {
        if (!is_null($this->cachedAllFieldsData)) {
            return $this->cachedAllFieldsData;
        }

        $fields = $get('../../../../../custom_fields') ?? [];
        $fields = new CustomFieldStateCast()->flattCustomFields($fields);

        return $this->cachedAllFieldsData = $this->getFieldDataFromFormData($fields, $formConfiguration);
    }

    public function getTargetFieldData(Get $get): EmbedCustomField|null
    {
        $identifier = $get('target');

        if (is_null($identifier)) {
            return null;
        }

        $fields = $this->getAllFieldsData($get, $this->getFormConfiguration($get));

        return $fields[$identifier] ?? null;
    }

    public function getFormConfiguration(Get $get): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($get('../../../../../custom_form_identifier'));
    }
}
