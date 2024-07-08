<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;


use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\EditHelper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;

abstract class FormEditorFieldAdder extends Group {
    public static function make(array|\Closure $schema = []): static {
        $static = app(static::class);
        $static->configure();
        return $static;
    }

    public function getChildComponents(): array
    {
        return array_merge([
            Placeholder::make($this->getTitle())
        ], parent::getChildComponents());
    }


    protected function addNewField($component, array $arguments, array $fieldData): void {
        $pos = $arguments['position'];

        $type = CustomFieldUtils::getFieldTypeFromRawDate($fieldData);

        if($type instanceof CustomLayoutType) $fieldData[CustomField::getEndContainerPositionAttribute()] = $pos;

        $path = $component->getStatePath(). '.custom_fields';
        $state = $component->getGetCallback()($path, true);

        $state = EditCustomFormHelper::addField($fieldData, $pos , $state);

        if(!empty($arguments['targetIn'])){
            $targetIn = $arguments['targetIn'];
            $targetInEndPos = $state[$targetIn][CustomField::getEndContainerPositionAttribute()];
            if($targetInEndPos < $pos){
                $state[$targetIn][CustomField::getEndContainerPositionAttribute()] = $pos;
            }
        }

        $component->getSetCallback()($path, $state, true);
    }

    protected abstract function getTitle():string;
}
