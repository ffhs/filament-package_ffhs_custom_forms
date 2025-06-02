<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\DynamicFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Support\Colors\Color;

final class TemplateFieldAdder extends FormEditorFieldAdder
{
    use CanModifyCustomFormEditorData;

    public function getGeneralFieldSelectOptions(): array
    {
        return once(function () {
            $customFormIdentifierString = $this->getGetCallback()('custom_form_identifier');
            /**@var DynamicFormConfiguration $customFormIdentifier */
            $customFormIdentifier = DynamicFormConfiguration::getFormConfigurationClass($customFormIdentifierString);

            return CustomForm::getTemplateTypesToAdd($customFormIdentifier)
                ->pluck('short_title', 'id')
                ->toArray();
        });
    }

    public function isTemplateDisabled($value): bool
    {
        if ($this->useTemplateUsedGeneralFields($value)) {
            return true;
        }

        $usedTemplateIds = //Cache::remember($this->getState()['id'] . '_template_fields_not_allowed_in_form', 1,
            once(function () {
                $templates = array_filter($this->getState()['custom_fields'], fn($da) => !empty($da['template_id']));
                return array_map(fn($template) => $template['template_id'], $templates);
            });
        return in_array($value, $usedTemplateIds, false);
    }

    public function useTemplateUsedGeneralFields(int $templateId): bool
    {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck('id')->toArray();
        $existingIds = $this->getUsedGeneralFieldIds($this->getState()['custom_fields']);
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

    protected function setUp(): void
    {
        $this->hidden(fn($state) => $state['is_template']);
        $this->label(__('filament-package_ffhs_custom_forms::custom_forms.navigation.templates'));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup('custom_fields')
                ->options($this->getGeneralFieldSelectOptions(...))
                ->disableOptionWhen($this->isTemplateDisabled(...))
                ->color(Color::Green)
                ->action(fn($option) => AddTemplateFieldAction::make('addTemplate')->option($option))
        ]);
    }
}
