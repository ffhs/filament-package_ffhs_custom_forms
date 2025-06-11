<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TitleTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Placeholder {
        $title = $this->getTitle($record);

        /**@var $placeholder Placeholder */
        $placeholder = $this->makeComponent(Placeholder::class, $record);
        return $placeholder
            ->content(new HtmlString($title))
            ->label('');
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return \Filament\Infolists\Components\Group::make()->hidden();
        }

        $title = $this->getTitle($record);

        /**@var $placeholder TextEntry */
        $placeholder = $this->makeComponent(TextEntry::class, $record);

        return $placeholder
            ->state(new HtmlString($title))
            ->columnSpanFull()
            ->inlineLabel();
    }

    private function getTitle($record): string
    {
        $titleSize = $this->getOptionParameter($record, 'title_size');

        if ($titleSize === 3) {
            $textClass = 'class="text-xl"';
        } elseif ($titleSize < 3) {
            $textClass = 'class="text-' . (4 - $titleSize) . 'xl"';
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
