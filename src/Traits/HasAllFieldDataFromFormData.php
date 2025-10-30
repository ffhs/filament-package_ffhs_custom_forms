<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFieldDataContainer;
use Illuminate\Support\Collection;

trait HasAllFieldDataFromFormData
{
    use CanLoadCustomFormEditorData;
    use CanLoadFieldRelationFromForm;

    /**
     * @param array<string, mixed> $fields
     * @param CustomFormConfiguration $configuration
     * @return Collection<string, EmbedCustomField>
     */
    protected function getFieldDataFromFormData(array $fields, CustomFormConfiguration $configuration): Collection
    {
        $fieldsFromTemplate = collect($fields)
            ->whereNotNull('template_id')
            ->flatMap(function ($templateData) use ($configuration) {
                $template = $configuration->getAvailableTemplates()->find($templateData['template_id']);
                /** @phpstan-ignore-next-line */
                return $template?->getCustomFields();
            })
            ->keyBy(fn(EmbedCustomField $customField) => $customField->identifier());

        return collect($fields)
            ->mapWithKeys(function (array $field) use ($configuration) {
                $customField = CustomFieldDataContainer::make($field, $configuration);
                return [$customField->identifier => $customField];
            })
            ->merge($fieldsFromTemplate);
    }
}
