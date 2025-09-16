<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Illuminate\Support\Collection;

class CustomFormDataContainer implements EmbedCustomForm
{
    protected CustomFormConfiguration $cachedFormConfiguration;

    public function __construct(protected $data)
    {
    }

    public static function make(array $data)
    {
        return app(static::class, ['data' => $data]);
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        if (isset($this->cachedFormConfiguration)) {
            return $this->cachedFormConfiguration;
        }

        return $this->cachedFormConfiguration = CustomForms::getFormConfiguration($this->data['custom_form_identifier'] ?? null);
    }

    public function getOwnedFields(): Collection
    {
        $formConfiguration = $this->getFormConfiguration();
        $customFields = $this->data['custom_fields'] ?? [];
        $customFields = array_map(static function ($field) use ($formConfiguration) {
            return CustomFieldDataContainer::make($field, $formConfiguration);
        }, $customFields);
        return collect($customFields);
    }


    public function customFields(): Collection
    {
        return $this->getOwnedFields()
            ->map(function (EmbedCustomField $field) {
                $fields = [$field];

                if ($template = $field->getTemplate()) {
                    return $template->getOwnedFields()->merge($fields);
                }

                return $fields;
            })
            ->flatten(1);

    }
}
