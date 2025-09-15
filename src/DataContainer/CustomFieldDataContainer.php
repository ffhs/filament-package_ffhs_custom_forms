<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;


class CustomFieldDataContainer implements EmbedCustomField
{
    public function __construct(protected array $data, protected CustomFormConfiguration $customFormConfiguration)
    {
    }

    public static function make(array $data, CustomFormConfiguration $customFormConfiguration)
    {
        return app(static::class, ['data' => $data, 'customFormConfiguration' => $customFormConfiguration]);
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'template' => $this->customFormConfiguration->getAvailableTemplates()[$this->data['template_id']],
            'name' => $this->data['name'][app()->getLocale()] ?? $this->data['name'][app()->getFallbackLocale()] ?? '',
            'identifier' => $this->data['identifier'] ?? $this->getGeneralField()->identifier,
            default => $this->data[$name] ?? null
        };
    }

    public function getType(): ?CustomFieldType
    {
        return CustomForms::getFieldTypeFromRawDate($this->data, $this->customFormConfiguration);
    }

    public function isGeneralField(): bool
    {
        return $this->data['general_field_id'] ?? false;
    }

    public function getGeneralField(): ?GeneralField
    {
        return $this->customFormConfiguration->getAvailableGeneralFields()[$this->data['general_field_id']] ?? null;
    }

    public function getTemplate(): ?EmbedCustomForm
    {
        // TODO: Implement getTemplate() method.
    }
}
