<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\Default;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

final class TemplateFieldAdder extends FormEditorFieldAdder
{
    use CanModifyCustomFormEditorData;

    public function isTemplateDisabled($value, $record): bool
    {
        if ($this->useTemplateUsedGeneralFields($value, $record)) {
            return true;
        }

        $usedTemplateIds =
            once(function () {
                $templates = array_filter($this->getState()['custom_fields'], fn($da) => !empty($da['template_id']));
                return array_map(fn($template) => $template['template_id'], $templates);
            });

        return in_array($value, $usedTemplateIds, false);
    }

    public function useTemplateUsedGeneralFields(int $templateId, $form): bool
    {
        $existingIds = $this->getUsedGeneralFieldIds($this->getState()['custom_fields'], $form);

        if (empty($existingIds)) {
            return false;
        }

        $configuration = $this->getCustomFormConfiguration();
        /** @var CustomForm $template */
        $template = $configuration->getAvailableTemplates()->get($templateId);
        $templateGenIds = $template->customFields
            ->whereNotNull('general_field_id')
            ->pluck('general_field_id')
            ->toArray();

        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

    public function getCustomFormConfiguration(): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($this->getGetCallback()('custom_form_identifier'));
    }

    protected function getUsableTemplates(): array
    {
        return once(function () {
            return $this->getCustomFormConfiguration()->getAvailableTemplates()
                ->pluck('short_title', 'id')
                ->toArray();
        });
    }

    protected function setUp(): void
    {
        $this->hidden(fn($state) => $state['is_template']);
        $this->label(__('filament-package_ffhs_custom_forms::custom_forms.navigation.templates'));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup(fn(Get $get) => 'custom_fields-' . $get('custom_form_identifier'))
                ->options($this->getUsableTemplates(...))
                ->disableOptionWhen($this->isTemplateDisabled(...))
                ->color(Color::Green)
                ->action(fn($option) => AddTemplateFieldAction::make('addTemplate')->option($option))
        ]);
    }
}
