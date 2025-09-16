<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

class ImageTypeView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        /**@var $placeholder Placeholder */
        $placeholder = $this->makeComponent(Placeholder::class, $record, false);

        return $placeholder
            ->content(
                new HtmlString(
                    app(Infolist::class)
                        ->columns(1)
                        ->schema([$this->getImageEntry($record)])
                        ->record($record)
                        ->render()
                )
            )
            ->label('');
    }

    public function getEntryComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): Component {
        if (!$this->getOptionParameter($record, 'show_in_view')) {
            return Group::make()
                ->hidden();
        }

        return $this->getImageEntry($record->customField);
    }

    private function getImageEntry(CustomField $record): ImageEntry
    {
        return ImageEntry::make('customField.options.image')
            ->label($this->getOptionParameter($record, 'show_label') ? $this->getLabelName($record) : '')
            ->checkFileExistence(false)
            ->visibility('private')
            ->state(array_values($this->getOptionParameter($record, 'image'))[0] ?? null)
            ->disk($this->getTypeConfigAttribute($record, 'disk'))
            ->columnSpan(2)
            ->height($this->getOptionParameter($record, 'height'))
            ->width($this->getOptionParameter($record, 'width'));
    }
}
