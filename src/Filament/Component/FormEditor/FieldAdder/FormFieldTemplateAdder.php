<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Forms\Components\Concerns\CanGenerateUuids;
use Filament\Support\Colors\Color;
use Filament\Support\Components\Component;

class FormFieldTemplateAdder extends FormFieldAdder
{
    use CanModifyCustomFormEditorData;
    use CanGenerateUuids;

    public static function getSiteComponent(CustomFormConfiguration $configuration): Component
    {
        return self::make('add_template_node')
            ->formConfiguration($configuration);
    }

    protected function useTemplateUsedGeneralFields(int $templateId): bool
    {
        $existingIds = $this->getUsedGeneralFieldIds($this->getCustomFieldsState(), $this->getFormConfiguration());

        if (empty($existingIds)) {
            return false;
        }

        $configuration = $this->getFormConfiguration();
        /** @var CustomForm $template */
        $template = $configuration
            ->getAvailableTemplates()
            ->get($templateId);
        $templateGenIds = $template
            ->customFields
            ->whereNotNull('general_field_id')
            ->pluck('general_field_id')
            ->toArray();
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

    protected function isTemplateDisabled($value): bool
    {
        if ($this->useTemplateUsedGeneralFields($value)) {
            return true;
        }

        $usedTemplateIds = once(function () {
            $templates = array_filter($this->getCustomFieldsState(), fn($da) => !empty($da['template_id']));

            return array_map(fn($template) => $template['template_id'], $templates);
        });

        return in_array($value, $usedTemplateIds, true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(3)
            ->visible(fn($get) => is_null($get('template_identifier')))
            ->label(CustomForm::__('pages.template_adder.label'))
            ->disableOptionWhen($this->isTemplateDisabled(...))
            ->options($this->getUsableTemplates(...))
            ->action($this->onAdd(...))
            ->color(Color::Green)
            ->expandSelect();
    }

    protected function onAdd(array $arguments): void
    {
        $this->addNewField([
            'identifier' => $this->generateUuid(),
            'template_id' => $arguments['option'],
            'is_active' => true,
        ]);
    }

    protected function getUsableTemplates(): array
    {
        return once(fn() => $this
            ->getFormConfiguration()
            ->getAvailableTemplates()
            ->pluck('short_title', 'id')
            ->toArray());
    }
}
