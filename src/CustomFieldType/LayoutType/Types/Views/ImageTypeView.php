<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Infolists\Components\ImageEntry;
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
        return $this->getImageEntry($customFieldAnswer->getCustomField())
            ->hidden(!$this->getOptionParameter($customFieldAnswer, 'show_in_view'));
    }

    protected function getImageEntry(EmbedCustomField $customField): Component
    {
        return $this->makeComponent(ImageEntry::class, $customField, false) //toDo fix
        ->label($this->getLabelName($customField))
            ->hiddenLabel(!$this->getOptionParameter($customField, 'show_label'))
            ->defaultImageUrl($this->getOptionParameter($customField, 'image'))
            ->state($this->getOptionParameter($customField, 'image'))
            ->disk($this->getTypeConfigAttribute($customField, 'disk'))
            ->imageHeight($this->getOptionParameter($customField, 'height'))
            ->imageWidth($this->getOptionParameter($customField, 'width'))
            ->visibility($this->getTypeConfigAttribute($customField, 'visibility'))
            ->checkFileExistence()
            ->columnSpan(2);
    }
}
