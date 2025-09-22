<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasFieldsMapToSelectOptions
{
    protected function getSelectOptionsFromFields(Collection $customFields, Get $get): array
    {
        $options = [];
        $formConfiguration = $this->getFormConfiguration($get);

        Log::info($customFields);
        foreach ($customFields as $field) {
            /**@var EmbedCustomField $field */
            $title = '';

            if ($field instanceof CustomField) {
                $template = $formConfiguration->getAvailableTemplates()->get($field->custom_form_id);
                $title = !is_null($template) ? $template->short_title : '';
            }

            $title = empty($title) ? 'Aktuelles Formular' : $title;
            $name = $field->isGeneralField() ? $field->getGeneralField()->name : $field->name;
            $options[$title][$field->identifier] = empty($name) ? $this->getDefaultFieldName($field) : $name;
        }

        return $options;
    }

    protected function getDefaultFieldName(EmbedCustomField $field): string
    {
        return 'No Name: ' . $field->getType()->displayname();
    }
}
