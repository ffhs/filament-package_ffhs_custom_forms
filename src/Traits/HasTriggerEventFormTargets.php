<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

trait HasTriggerEventFormTargets
{
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
            ->label('Target')
            ->options($this->getTargetOptions(...))
            ->live();
    }

    public function getTargetOptions(Get $get, ?CustomForm $record): array
    {
        $fields = collect($this->getAllFieldsData($get, $record))
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
