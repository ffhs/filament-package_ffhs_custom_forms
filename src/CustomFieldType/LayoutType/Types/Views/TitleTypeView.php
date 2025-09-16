<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class TitleTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $title = $this->getTitle($customField);

        /**@var $placeholder Placeholder */
        $placeholder = $this->makeComponent(Placeholder::class, $customField, false);

        return $placeholder
            ->content(new HtmlString($title))
            ->label('');
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        if (!$this->getOptionParameter($customFieldAnswer, 'show_in_view')) {
            return Group::make()
                ->hidden();
        }

        $title = $this->getTitle($customFieldAnswer);

        /**@var $placeholder TextEntry */
        $placeholder = $this->makeComponent(TextEntry::class, $customFieldAnswer, true);

        return $placeholder
            ->state(new HtmlString($title))
            ->columnSpanFull()
            ->inlineLabel();
    }

    private function getTitle($record): string
    {
        $titleSize = $this->getOptionParameter($record, 'title_size');

        if ($titleSize < 3) {
            $textClass = 'class="text-' . (4 - $titleSize) . 'xl"';
        } elseif ($titleSize === 3) {
            $textClass = 'class="text-xl"';
        } elseif ($titleSize === 4) {
            $textClass = 'class="text-lg"';
        } elseif ($titleSize === 5) {
            $textClass = 'class="text-base"';
        } elseif ($titleSize === 6) {
            $textClass = 'class="text-sm"';
        } else {
            $textClass = 'class="text-xs"';
        }

        $titleText = $this->getLabelName($record);

        return '<h' . $titleSize . ' ' . $textClass . '>' . $titleText . ' </h' . $titleSize . '> ';
    }
}
