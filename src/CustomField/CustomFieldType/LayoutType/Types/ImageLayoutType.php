<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\ImageTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageLayoutType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "image_layout";
    }

    public function viewModes(): array
    {
        return [
            'default' => ImageTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-card-image";
    }

    public function extraTypeOptions(): array
    {
        return [
            TypeOptionGroup::make("Data", [
                'image' => new FastTypeOption([],
                    FileUpload::make('image')
                        ->directory($this->getConfigAttribute("save_path"))
                        ->disk($this->getConfigAttribute("disk"))
                        ->label("Bild") //ToDo Translate
                        ->visibility('private')
                        ->columnSpanFull()
                        ->downloadable()
                        ->previewable()
                        ->image()
                        ->live()
                ),
            ], "carbon-data-1"),
            LayoutOptionGroup::make()
                ->removeTypeOption('inline_label')
                ->mergeTypeOptions([
                    'column_span' => new ColumnSpanOption(),
                    'width' => new FastTypeOption(
                        null,
                        TextInput::make('width')
                            ->label("Breite") //ToDo Translate
                            ->minValue(1)
                            ->numeric()
                    ),
                    'show_title' => (ShowTitleOption::make())->modifyDefault(fn($default) => false),
                    'show_in_view' => (ShowInViewOption::make())->modifyDefault(fn($default) => false),
                ]),

        ];
    }


    public function canBeRequired(): bool
    {
        return false;
    }

}
