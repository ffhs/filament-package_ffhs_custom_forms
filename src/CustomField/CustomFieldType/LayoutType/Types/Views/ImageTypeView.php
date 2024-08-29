<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldMapper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;

class ImageTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): \Filament\Forms\Components\Component {

        return Placeholder::make(FieldMapper::getIdentifyKey($record))
            ->label("")
            ->columnSpanFull()
            ->content(new HtmlString(
                (new Infolist())->columns(1)->schema([ self::getImageEntry($record)])->record($record)->render()
            ));

    }

    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record,
                                                array           $parameter = []): Component{

        if(!FieldMapper::getOptionParameter($record,"show_in_view"))
            return Group::make()->hidden();

        return self::getImageEntry($record->customField);
    }

    private static function getImageEntry(CustomField $record): ImageEntry {
        return ImageEntry::make('customField.options.image')
            ->label(FieldMapper::getOptionParameter($record,"show_title")? FieldMapper::getLabelName($record):"")
            ->checkFileExistence(false)
            ->visibility('private')
            ->state(array_values($record->options["image"])[0])
            ->disk(FieldMapper::getTypeConfigAttribute($record,"disk"))
            ->columnSpan(2)
            ->height(FieldMapper::getOptionParameter($record,'height'))
            ->width(FieldMapper::getOptionParameter($record,'width'));
    }


}
