<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Support\Colors\Color;

trait HasEditorLayoutElements
{
    abstract public function icon(): string;

    public function getEditorFieldBadge(array $rawData): ?string
    {
        $text = $this->getEditorFieldBadgeText($rawData);
        $color = $this->getEditorFieldBadgeColor($rawData);

        if (is_null($color) || is_null($text)) {
            return null;
        }

        return view('filament-package_ffhs_custom_forms::badge', ['text' => $text, 'color' => $color]);
    }

    public function getEditorFieldTitle(array $rawData, CustomForm $form): string
    {
        $customField = $this->getEditorCustomFieldFromData($rawData, $form);

        if (!$customField->isGeneralField()) {
            return $this->getTranslatedName();
        }

        return $customField->name;
    }

    public function getEditorFieldIcon(array $rawData, CustomForm $form): string
    {
        $customField = $this->getEditorCustomFieldFromData($rawData, $form);

        if (!$customField->isGeneralField()) {
            return $this->icon();
        }

        return $customField->generalField->icon;
    }

    public function getFieldDataExtraComponents(CustomFormConfiguration $configuration, array $state): array
    {
        return [];
    }

    public function getEditorActions(CustomFormConfiguration $formConfiguration, array $rawData): array
    {
        return [
            DefaultCustomFieldDeleteAction::make('delete-field')
                ->formConfiguration($formConfiguration),
//            DefaultCustomFieldEditTypeOptionsAction::make('edit-field-' . $key),
//            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return empty($fielData['general_field_id']);
    }

    protected function getEditorCustomFieldFromData(array $rawData, CustomForm $form): CustomField
    {
        $customField = app(CustomField::class)->fill($rawData);

        if (!$customField->isGeneralField()) {
            return $customField;
        }

        $generalField = $form
            ->getFormConfiguration()
            ->getAvailableGeneralFields()
            ->get($customField->general_field_id);
        $customField->setRelation('generalField', $generalField);

        return $customField;
    }

    protected function getEditorFieldBadgeText(array $rawData): ?string
    {
        $customField = app(CustomField::class)->fill($rawData);

        return $customField->isGeneralField() ? 'Gen' : null;
    }

    protected function getEditorFieldBadgeColor(array $rawData): ?array
    {
        return Color::rgb('rgb(43, 164, 204)');
    }
}
