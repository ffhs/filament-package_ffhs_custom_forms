<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\TypeActions\DefaultFieldEditOptionsAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Support\Colors\Color;

trait HasEditorLayoutElements
{
    abstract public function icon(): string;

    public function getEditorFieldBadge(array $fieldState, CustomFormConfiguration $configuration): ?string
    {
        $text = $this->getEditorFieldBadgeText($fieldState);
        $color = $this->getEditorFieldBadgeColor($fieldState);

        if (is_null($color) || is_null($text)) {
            return null;
        }

        return view(
            'filament-package_ffhs_custom_forms::filament.components.form-editor.badge',
            ['text' => $text, 'color' => $color]
        );
    }

    public function getEditorFieldTitle(array $fieldState, CustomFormConfiguration $configuration): string
    {
        $customField = $this->getEditorCustomFieldFromData($fieldState, $configuration);

        if (!$customField->isGeneralField()) {
            return $this->displayname();
        }

        return str($customField->getGeneralField()->name ?? '404');
    }

    public function getEditorFieldIcon(array $rawData, CustomFormConfiguration $configuration): string
    {
        $customField = $this->getEditorCustomFieldFromData($rawData, $configuration);

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
                ->formConfiguration($formConfiguration)
                ->visible($this->canBeDeactivate()),
        ];
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return empty($fielData['general_field_id']);
    }

    protected function getEditorCustomFieldFromData(array $rawData, CustomFormConfiguration $configuration): CustomField
    {
        $customField = app(CustomField::class)->fill($rawData);

        if (!$customField->isGeneralField()) {
            return $customField;
        }

        $generalField = $configuration
            ->getAvailableGeneralFields()
            ->get($customField->general_field_id);
        $customField->setRelation('generalField', $generalField);

        return $customField;
    }

    protected function getEditorFieldBadgeText(array $fielData): ?string
    {
        $customField = app(CustomField::class)->fill($fielData);

        return $customField->isGeneralField() ? 'Gen' : null;
    }

    protected function getEditorFieldBadgeColor(array $fielData): ?array
    {
        return Color::rgb('rgb(43, 164, 204)');
    }
}
