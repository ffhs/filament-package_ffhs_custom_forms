<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCreateFieldManager;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\EditCustomFormFields;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;
use Filament\Forms\Set;

abstract class EditCreateFieldAction extends Action
{
    //Needs to implement createField()
    protected function setUp(): void {
        parent::setUp();
        $this->action($this->createField(...));
    }

    protected function addNewField(EditCustomFormFields $component, array $arguments, array $fieldData): void {
        $pos = $arguments['formPosition'];


        $type = CustomFieldUtils::getFieldTypeFromRawDate($fieldData);

        if($type instanceof CustomLayoutType) $fieldData[CustomField::getEndContainerPositionAttribute()] = $pos;

        $path = $component->getStatePath();
        $state = $component->getGetCallback()($path, true);

        $state = EditCustomFormHelper::addField($fieldData, $pos , $state);

        $component->getSetCallback()($path, $state, true);
    }

}
