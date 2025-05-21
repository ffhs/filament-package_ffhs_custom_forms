<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Traits\HasDefaultViewComponent;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class TitleTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Placeholder {
        $title = self::getTitle($record);

        /**@var $placeholder Placeholder */
        $placeholder = static::makeComponent(Placeholder::class, $record);
        return $placeholder
            ->content(new HtmlString($title))
            ->label("");
    }

    private static function getTitle($record): string
    {
        $titleSize = FieldMapper::getOptionParameter($record, "title_size");

        if ($titleSize == 3) {
            $textClass = 'class="text-xl"';
        } elseif ($titleSize < 3) $textClass = 'class="text-' . (4 - $titleSize) . 'xl"';
        else {
            if ($titleSize == 4) {
                $textClass = 'class="text-lg"';
            } elseif ($titleSize == 5) $textClass = 'class="text-base"';
            elseif ($titleSize == 6) $textClass = 'class="text-sm"';
            else $textClass = 'class="text-xs"';
        }
        $titleText = FieldMapper::getLabelName($record);
        return '<h' . $titleSize . ' ' . $textClass . '>' . $titleText . ' </h' . $titleSize . '> ';
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return \Filament\Infolists\Components\Group::make()->hidden();
        }

        $title = self::getTitle($record);

        /**@var $placeholder TextEntry */
        $placeholder = static::makeComponent(TextEntry::class, $record);

        return $placeholder
            ->state(new HtmlString($title))
            ->columnSpanFull()
            ->inlineLabel();
    }

}
