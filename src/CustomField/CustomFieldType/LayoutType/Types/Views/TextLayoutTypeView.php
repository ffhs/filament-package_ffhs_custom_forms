<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TextLayoutTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Placeholder {
        $text = FieldMapper::getOptionParameter($record, "text")[app()->getLocale()] ?? "";

        /**@var $placeholder Placeholder */
        $placeholder = static::makeComponent(Placeholder::class, $record);
        return $placeholder
            ->content(new HtmlString($text))
            ->label("");
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
//        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
//            return Group::make()->hidden();
//        }

        $label = FieldMapper::getOptionParameter($record, "show_label") ? FieldMapper::getLabelName($record) : "";
        $text = FieldMapper::getOptionParameter($record, "text")[app()->getLocale()] ?? "";

        /**@var $placeholder TextEntry */
        $placeholder = static::makeComponent(TextEntry::class, $record);

        return $placeholder
            ->state(new HtmlString($text))
            ->label($label)
            ->columnSpanFull()
            ->inlineLabel();
    }


}
