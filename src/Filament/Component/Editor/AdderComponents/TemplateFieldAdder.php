<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

final class TemplateFieldAdder extends SimpleAdder
{
    function getTitle(): string {
       return __("filament-package_ffhs_custom_forms::custom_forms.navigation.templates");
    }

    protected function setUp(): void {
        parent::setUp();
        $this->hidden(fn($state) => $state['is_template']);
    }


    function getFieldsToAdd(): array {
        return  CustomForm::getTemplateTypesToAdd($this->getRecord()->getFormConfiguration())
            ->pluck("short_title", "id")
            ->toArray();
    }

    function getAddMode(): string {
        return 'template';
    }

    function getBorderColor(): string {
        return '#206e0e';
    }

    function getDisabledColor(): string {
        return '#404E3F';
    }

    function getHoverColor(): string {
        return '#abbaa7';
    }

    function getAdderId(): string {
        return "templateAdder";
    }

    public function isOptionDisables($id): bool {
        if($this->useTemplateUsedGeneralFields($id)) return true;

        $templates =array_filter($this->getState()['custom_fields'], fn($da) => !empty($da['template_id']));
        $usedTemplateIds = array_map(fn($template) => $template["template_id"],$templates);

        return in_array($id,$usedTemplateIds);
    }

    public function useTemplateUsedGeneralFields(int $templateId): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = EditCustomFormHelper::getUsedGeneralFieldIds($this->getState()['custom_fields']);
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

}
