<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\ImageTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;
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
                    'show_label' => (ShowLabelOption::make())->modifyDefault(fn($default) => false),
                    'show_in_view' => (ShowInViewOption::make())->modifyDefault(fn($default) => false),
                ]),

        ];
    }


    public function canBeRequired(): bool
    {
        return false;
    }

}
