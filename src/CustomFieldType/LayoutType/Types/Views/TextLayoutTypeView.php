<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class TextLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $text = $this->getOptionParameter($customField, 'text')[app()->getLocale()] ?? '';

        return $this->makeComponent(TextEntry::class, $customField, false)
            ->state(new HtmlString($text))
            ->hiddenLabel();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $text = $this->getOptionParameter($customFieldAnswer, 'text')[app()->getLocale()] ?? '';

        return $this->makeComponent(TextEntry::class, $customFieldAnswer, true)
            ->state(new HtmlString($text))
            ->hiddenLabel();
    }
}
