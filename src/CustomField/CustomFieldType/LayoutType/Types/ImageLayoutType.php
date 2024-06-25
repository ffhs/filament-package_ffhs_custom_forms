<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\ImageTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageLayoutType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function identifier(): string {
        return "image_layout";
    }

    public function viewModes(): array {
        return [
          'default' => ImageTypeView::class
        ];
    }

    public function icon(): string {
       return "bi-card-image";
    }

    public function extraTypeOptions(): array {
        return [
            'image'=> new FastTypeOption([],
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
            'column_span' => new ColumnSpanOption(),

            'height' => new FastTypeOption(null,
                TextInput::make('height')
                    ->columnStart(1)
                    ->minValue(1)
                    ->label("Höhe") //ToDo Translate
                    ->numeric()
            ),
            'width' =>new FastTypeOption(null,
                TextInput::make('width')
                    ->label("Breite") //ToDo Translate
                    ->minValue(1)
                    ->numeric()
            ),

            'new_line_option' => (new NewLineOption())->modifyComponent(fn($component)=> $component->columnStart(1)),
            'show_title' => (new ShowTitleOption())->modifyDefault(fn($default) => false),
            'show_in_view'=> (new ShowInViewOption())->modifyDefault(fn($default) => false),
        ];
    }


    public function canBeRequired(): bool {
        return false;
    }

}
