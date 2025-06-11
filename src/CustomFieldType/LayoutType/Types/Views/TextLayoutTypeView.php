<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TextLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Placeholder {
        $text = $this->getOptionParameter($record, 'text')[app()->getLocale()] ?? '';

        /**@var $placeholder Placeholder */
        $placeholder = $this->makeComponent(Placeholder::class, $record);
        return $placeholder
            ->content(new HtmlString($text))
            ->label('');
    }

    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return Group::make()->hidden();
        }

        $label = $this->getOptionParameter($record, 'show_label') ? $this->getLabelName($record) : '';
        $text = $this->getOptionParameter($record, 'text')[app()->getLocale()] ?? '';

        /**@var $placeholder TextEntry */
        $placeholder = $this->makeComponent(TextEntry::class, $record);

        return $placeholder
            ->state(new HtmlString($text))
            ->label($label)
            ->columnSpanFull()
            ->inlineLabel();
    }


}
