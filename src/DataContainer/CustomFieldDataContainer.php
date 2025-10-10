<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Support\Collection;


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
        /**@phpstan-ignore-next-line */
        return $this->customFormConfiguration->getAvailableGeneralFields()[$this->data['general_field_id']] ?? null;
    }

    public function getTemplate(): ?EmbedCustomForm
    {
        /**@phpstan-ignore-next-line */
        return $this->customFormConfiguration->getAvailableTemplates()->get($this->data['template_id'] ?? '');
    }

    public function getCustomOptions(): Collection
    {
        $options = $this->data['options']['custom_options'] ?? $this->data['options']['customOptions'] ?? [];
        return collect($options);
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        return $this->customFormConfiguration;
    }

    public function isTemplate(): bool
    {
        return (bool)($this->data['template_id'] ?? false);
    }

    public function identifier(): string
    {
        return $this->__get('identifier');
    }

    public function getLayoutEndPosition(): ?int
    {
        return $this->__get('layout_end_position');
    }

    public function getFormPosition(): int
    {
        return $this->__get('form_position');
    }

    public function isActive(): bool
    {
        return $this->__get('active');
    }

    public function getOptions(): array
    {
        return $this->__get('options') ?? [];
    }

    public function setOptions(array $options): void
    {
        $this->data['options'] = $options;
    }

    public function getName(): ?string
    {
        return $this->__get('name');
    }
}
