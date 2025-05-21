<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;

trait HasTypeView
{
    public function getFormComponent(CustomField $record, CustomForm $form, string $viewMode = "default",
        array $parameter = []): Component { //ToDo Remove Parameters?
        $viewMods = $this->getViewModes($form->getFormConfiguration());
        //FieldTypeView.php
        if (empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this, $record, $parameter);
        return ($viewMods[$viewMode])::getFormComponent($this, $record, $parameter);
    }

    public function getInfolistComponent(CustomFieldAnswer $record, CustomForm $form, string $viewMode = "default",
        array $parameter = []): \Filament\Infolists\Components\Component {
        $viewMods = $this->getViewModes($form->getFormConfiguration());
        //FieldTypeView.php
        if (empty($viewMods[$viewMode])) return ($viewMods["default"])::getFormComponent($this, $record, $parameter);
        return ($viewMods[$viewMode])::getInfolistComponent($this, $record, $parameter);
    }

    public function getViewModes(null|string|DynamicFormConfiguration $dynamicFormConfiguration = null): array {
        $viewMods = $this->viewModes();

        /**@var DynamicFormConfiguration $dynamicFormConfig */
        $dynamicFormConfig = new $dynamicFormConfiguration();

        //Config Overwrite
        $overWrittenLevelOne = $this->overwriteViewModes();
        if (!empty($overWrittenLevelOne)) foreach ($overWrittenLevelOne as $key => $value) {
            $viewMods[$key] = $value;
        }

        if (is_null($dynamicFormConfiguration)) return $this->viewModes();

        // Form Overwritten
        $overWrittenLevelTwo = $dynamicFormConfig::overwriteViewModes();
        if (!empty($overWrittenLevelTwo) && !empty($overWrittenLevelTwo[$this::class])) {
            foreach ($overWrittenLevelTwo[$this::class] as $key => $value) {
                $viewMods[$key] = $value;
            }
        }

        return $viewMods;
    }

    public function overwriteViewModes(): array {
        $viewModes = config("ffhs_custom_forms.view_modes");
        if (empty($viewModes[$this::class])) return [];
        return $viewModes[$this::class];
    }

}
