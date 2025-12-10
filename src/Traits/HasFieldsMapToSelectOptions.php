<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;

trait HasFieldsMapToSelectOptions
{

    public function getFormConfiguration(Get $get): CustomFormConfiguration
    {
        return CustomForms::getFormConfiguration($get('../../../../../custom_form_identifier'));
    }

    /**
     * @param Collection<string, EmbedCustomField> $customFields
     * @param CustomFormConfiguration $formConfiguration
     * @return array<string|int, array<string, string>>
     */
    protected function getSelectOptionsFromFields(
        Collection $customFields,
        CustomFormConfiguration $formConfiguration
    ): array {
        $options = [];

        foreach ($customFields as $field) {
            /**@var EmbedCustomField $field */
            $title = '';

            if ($field instanceof CustomField) {
                /**@var CustomForm $template */
                $template = $formConfiguration->getAvailableTemplates()->get($field->custom_form_id);
                /** @phpstan-ignore-next-line */
                $title = !is_null($template) ? $template->short_title : '';
            }

            $currentFormTitle = FormRule::type__('events.has_targets.target_field.current_form');
            $title = empty($title) ? $currentFormTitle : $title;
            $name = $field->isGeneralField() ? $field->getGeneralField()->name : $field->getName();
            $options[$title][$field->identifier()] = empty($name) ? $this->getDefaultFieldName($field) : $name;
        }

        return $options;
    }

    protected function getDefaultFieldName(EmbedCustomField $field): string
    {
        return FormRule::type__('events.has_targets.target_field.no_name') . ':' . $field->getType()->displayname();
    }
}
