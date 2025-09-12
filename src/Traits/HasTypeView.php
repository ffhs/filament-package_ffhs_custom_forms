<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldTypeHasNoDefaultViewModeException;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Support\Components\Component;

trait HasTypeView
{
    public function getFormComponent(
        CustomField $customField,
        string $viewMode = 'default',
        array $parameter = [],
        ?CustomFormConfiguration $formConfiguration = null
    ): Component {
        $formConfiguration = $formConfiguration ?? $customField->customForm->getFormConfiguration();

        return $this
            ->getFieldTypeView($formConfiguration, $viewMode)
            ->getFormComponent($this, $customField, $parameter);
    }

    public function getInfolistComponent(
        CustomFieldAnswer $answer,
        string $viewMode = 'default',
        array $parameter = [],
        ?CustomFormConfiguration $formConfiguration = null
    ): Component {
        $formConfiguration = $formConfiguration ?? $answer->customForm->getFormConfiguration();

        return $this
            ->getFieldTypeView($formConfiguration, $viewMode)
            ->getInfolistComponent($this, $answer, $parameter);
    }

    public function getFieldTypeView(
        CustomFormConfiguration $formConfiguration,
        string $viewMode = 'default'
    ): FieldTypeView {
        $viewMods = $this->getViewModes($formConfiguration);

        if (empty($viewMods[$viewMode])) {
            $fieldTypeView = $viewMods['default'] ?? null;

            if (is_array($fieldTypeView)) {
                throw new FieldTypeHasNoDefaultViewModeException($this::identifier() . ' has no default view mode');
            }

            return $fieldTypeView;
        }

        return $viewMods[$viewMode];
    }

    public function getViewModes(CustomFormConfiguration $dynamicFormConfig): array
    {
        return once(function () use ($dynamicFormConfig) {
            $viewMods = $this->viewModes();

            foreach ($viewMods as $viewMode => $viewMod) {
                $viewMods[$viewMode] = $viewMod::make();
            }

            //Config Overwrite
            $overWrittenLevelOne = $this->overwriteViewModes();

            if (!empty($overWrittenLevelOne)) {
                foreach ($overWrittenLevelOne as $key => $value) {
                    $viewMods[$key] = $value::make();
                }
            }

            // Form Overwritten
            $overWrittenLevelTwo = $dynamicFormConfig::overwriteViewModes();

            if (!empty($overWrittenLevelTwo) && !empty($overWrittenLevelTwo[$this::class])) {
                foreach ($overWrittenLevelTwo[$this::class] as $key => $value) {
                    $viewMods[$key] = $value::make();
                }
            }

            return $viewMods;
        });
    }

    public function overwriteViewModes(): array
    {
        $viewModes = config('ffhs_custom_forms.view_modes');

        if (empty($viewModes[$this::class])) {
            return [];
        }

        return $viewModes[$this::class];
    }
}
