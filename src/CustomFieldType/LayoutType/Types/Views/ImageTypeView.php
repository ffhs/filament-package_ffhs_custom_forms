<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;

class ImageTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        return $this->getImageEntry($customField);
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make()->hidden();
        }

        return $this->getImageEntry($customFieldAnswer->getCustomField());
    }

    private function getImageEntry(EmbedCustomField $customField): ImageEntry
    {
        return ImageEntry::make('customField.options.image')
            ->label($this->getOptionParameter($customField, 'show_label') ? $this->getLabelName($customField) : '')
            ->checkFileExistence(false)
            ->visibility('private')
            ->state(array_values($this->getOptionParameter($customField, 'image'))[0] ?? null)
            ->disk($this->getTypeConfigAttribute($customField, 'disk'))
            ->columnSpan(2)
            ->imageHeight($this->getOptionParameter($customField, 'height'))
            ->imageWidth($this->getOptionParameter($customField, 'width'));
    }
}
