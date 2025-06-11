<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomActivationAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomFieldDeleteAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\TypeActions\Default\DefaultCustomFieldEditTypeOptionsAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
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

    public function getEditorFieldTitle(array $rawData): string
    {
        $customField = app(CustomField::class)->fill($rawData);
        if (!$customField->isGeneralField()) {
            return $this->getTranslatedName();
        }

        return $customField->name;
    }

    public function getEditorFieldIcon(array $rawData): string
    {
        $customField = app(CustomField::class)->fill($rawData);
        if (!$customField->isGeneralField()) {
            return $this->icon();
        }

        return $customField->generalField->icon;
    }

    public function fieldEditorExtraComponent(array $rawData): ?string
    {
        return null;
    }

    public function getEditorActions(string $key, array $rawData): array
    {
        return [
            DefaultCustomFieldDeleteAction::make('delete-field-' . $key),
            DefaultCustomFieldEditTypeOptionsAction::make('edit-field-' . $key),
            DefaultCustomActivationAction::make('active-' . $key)->visible($this->canBeDeactivate()),
        ];
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return empty($fielData['general_field_id']);
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
