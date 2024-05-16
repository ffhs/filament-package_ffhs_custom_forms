<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;

class ImageTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {

        return Placeholder::make(FormMapper::getIdentifyKey($record))
            ->label("")
            ->columnSpanFull()
            ->content(new HtmlString(
                (new Infolist())->columns(1)->schema([ self::getImageEntry($record)])->record($record)->render()
            ));

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
        array $parameter = []): Component{

        if(!FormMapper::getOptionParameter($record,"show_in_view"))
            return TextEntry::make(FormMapper::getIdentifyKey($record))->label("")->state("");

        return self::getImageEntry($record->customField);
    }

    private static function getImageEntry(CustomField $record): ImageEntry {
        return ImageEntry::make('customField.options.image')
            ->label(FormMapper::getOptionParameter($record,"show_title")? FormMapper::getLabelName($record):"")
            ->checkFileExistence(false)
            ->visibility('private')
            ->state(array_values($record->options["image"])[0])
            ->disk(FormMapper::getTypeConfigAttribute($record,"disk"))
            ->columnSpan(2)
            ->height(FormMapper::getOptionParameter($record,'height'))
            ->width(FormMapper::getOptionParameter($record,'width'));
    }


}
