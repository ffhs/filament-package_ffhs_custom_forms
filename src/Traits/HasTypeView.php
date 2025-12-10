<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\FieldTypeHasNoDefaultViewModeException;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Filament\Support\Components\Component;

trait HasTypeView
{
    /**
     * @param EmbedCustomField $customField
     * @param CustomFormConfiguration $formConfiguration
     * @param string $viewMode
     * @param array<string, mixed> $parameter
     * @return Component
     */
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

    /**
     * @param EmbedCustomFieldAnswer $answer
     * @param string $viewMode
     * @param array<string, mixed> $parameter
     * @param CustomFormConfiguration|null $formConfiguration
     * @return Component
     */
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
    }

    public function getFieldTypeView(
        CustomFormConfiguration $formConfiguration,
        string $viewMode = 'default'
    ): FieldTypeView {
        $viewMods = $this->getViewModes($formConfiguration);

        if (array_key_exists($viewMode, $viewMods)) {
            return $viewMods[$viewMode];
        }

        $fieldTypeView = $viewMods['default'] ?? null;

        if (!$fieldTypeView) {
            throw new FieldTypeHasNoDefaultViewModeException($this::identifier() . ' has no default view mode');
        }

        return $fieldTypeView;
    }

    /**
     * @param CustomFormConfiguration $dynamicFormConfig
     * @return array<string, FieldTypeView>
     */
    public function getViewModes(CustomFormConfiguration $dynamicFormConfig): array
    {
        return once(function () use ($dynamicFormConfig): array {
            // Basis View Modes initialisieren
            $viewMods = $this->viewModes();

            foreach ($viewMods as $viewModeKey => $viewMode) {
                $viewMods[$viewModeKey] = $viewMode::make();
            }

            //Config Overwrite
            $this->applyOverwrittenConfigViewModes($viewMods);
            // Form Overwritten
            $this->applyOverwrittenFormViewModes($viewMods, $dynamicFormConfig);


            return $viewMods;
        });
    }

    /**
     * @param array<string, FieldTypeView> $viewMods
     * @param CustomFormConfiguration $dynamicFormConfig
     * @return void
     */
    protected function applyOverwrittenFormViewModes(array &$viewMods, CustomFormConfiguration $dynamicFormConfig): void
    {
        $overWrittenLevelTwo = $dynamicFormConfig::overwriteViewModes();

        if (!empty($overWrittenLevelTwo) && !empty($overWrittenLevelTwo[$this::class])) {
            foreach ($overWrittenLevelTwo[$this::class] as $key => $value) {
                $viewMods[$key] = $value::make();
            }
        }
    }

    /**
     * @param array<string, FieldTypeView> $viewMods
     * @return void
     */
    protected function applyOverwrittenConfigViewModes(array &$viewMods): void
    {
        /** @var array<class-string<CustomFieldType>, array<string, class-string<FieldTypeView>>> $configViewModes */
        $configViewModes = CustomForms::config('view_modes', []);
        $overWrittenConfig = $configViewModes[$this::class] ?? [];

        foreach ($overWrittenConfig as $key => $value) {
            $viewMods[$key] = $value::make();
        }
    }
}
