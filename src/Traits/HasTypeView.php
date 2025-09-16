<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldTypeHasNoDefaultViewModeException;
use Filament\Support\Components\Component;

trait HasTypeView
{
    public function getFormComponent(
        EmbedCustomField $customField,
        CustomFormConfiguration $formConfiguration,
        string $viewMode = 'default',
        array $parameter = [],
    ): Component {

        return $this
            ->getFieldTypeView($formConfiguration, $viewMode)
            ->getFormComponent($customField, $parameter);
    }

    public function getEntryComponent(
        EmbedCustomFieldAnswer $answer,
        string $viewMode = 'default',
        array $parameter = [],
        ?CustomFormConfiguration $formConfiguration = null
    ): Component {
        $formConfiguration = $formConfiguration ?? $answer->getCustomForm()->getFormConfiguration();

        return $this
            ->getFieldTypeView($formConfiguration, $viewMode)
            ->getEntryComponent($answer, $parameter);
        //  ->getEntryComponent($this, $answer, $parameter);
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
