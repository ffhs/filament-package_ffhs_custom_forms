<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

trait HasTriggerEventFormTargets
{ //ToDo do it without customForms only with form Configuration
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;
    use CanLoadFieldRelationFromForm;

    protected array|null $cachedAllFieldsData = null;

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

    public function getTargetOptions(Get $get, ?CustomForm $record): array
    {
        $fields = collect($this->getAllFieldsData($get, $record))
            ->filter(fn($fieldData) => empty($fieldData['template_id']))
            ->map(function ($fieldData) use ($record) {
                $customField = new CustomField($fieldData);

                return $this->loadFieldRelationsFromForm($customField, $record);
            });

        return $this->getSelectOptionsFromFields($fields);
    }

    public function getAllFieldsData(Get $get, CustomForm $customForm): array
    {
        if (!is_null($this->cachedAllFieldsData)) {
            return $this->cachedAllFieldsData;
        }

        $fields = $get('../../../../../custom_fields') ?? [];
        $fields = new CustomFieldStateCast()->flattCustomFields($fields);

        return $this->cachedAllFieldsData = $this->getFieldDataFromFormData($fields, $customForm);
    }

    public function getTargetFieldData(Get $get, $customForm): array|null
    {
        $identifier = $get('target');

        if (is_null($identifier)) {
            return null;
        }

        $fields = $this->getAllFieldsData($get, $customForm);

        return $fields[$identifier] ?? null;
    }
}
