<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Support\Collection;

trait HasFieldsMapToSelectOptions
{
    protected function getSelectOptionsFromFields(Collection $customFields): array
    {
        $options = [];
        foreach ($customFields as $field) {
            /**@var CustomField $field */
            $title = '';
            if ($field->relationLoaded('customForm')) {
                $title = $field?->customForm?->short_title;
            }
            if (empty($title)) {
                $title = '?';
            }

            $name = $field->isTemplate() ? $field->template->short_title : $field->name;
            $options[$title][$field->identifier] = empty($name) ? $this->getDefaultFieldName($field) : $name;
        }

        return $options;
    }

    protected function getDefaultFieldName(CustomField $field): string
    {
        return 'No Name: ' . $field->getType()->getTranslatedName();
    }
}
