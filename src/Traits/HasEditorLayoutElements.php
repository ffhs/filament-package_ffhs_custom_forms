<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultFieldEditOptionsAction;
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

    public function getEditorActions(CustomFormConfiguration $formConfiguration, array $state): array
    {
        return [
            DefaultFieldDeleteAction::make('delete-field')
                ->formConfiguration($formConfiguration),
            DefaultFieldEditOptionsAction::make('edit-options')
                ->formConfiguration($formConfiguration),
            DefaultCustomActivationAction::make('toggle_active')
                ->visible($this->canBeDeactivate())
                ->formConfiguration($formConfiguration),
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
