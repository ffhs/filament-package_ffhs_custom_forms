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
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;

class ImageTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public static function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): \Filament\Forms\Components\Component {
        /**@var $placeholder Placeholder */
        $placeholder = static::makeComponent(Placeholder::class, $record);
        return $placeholder
            ->content(
                new HtmlString(
                    (new Infolist())->columns(1)->schema([self::getImageEntry($record)])->record($record)->render()
                )
            )
            ->label("");
    }

    private static function getImageEntry(CustomField $record): ImageEntry
    {
        return ImageEntry::make('customField.options.image')
            ->label(FieldMapper::getOptionParameter($record, "show_label") ? FieldMapper::getLabelName($record) : "")
            ->checkFileExistence(false)
            ->visibility('private')
            ->state(array_values(FieldMapper::getOptionParameter($record, 'image'))[0] ?? null)
            ->disk(FieldMapper::getTypeConfigAttribute($record, "disk"))
            ->columnSpan(2)
            ->height(FieldMapper::getOptionParameter($record, 'height'))
            ->width(FieldMapper::getOptionParameter($record, 'width'));
    }

    public static function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!FieldMapper::getOptionParameter($record, "show_in_view")) {
            return Group::make()->hidden();
        }

        return self::getImageEntry($record->customField);
    }


}
