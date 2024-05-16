<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\Toggle;

class FileUploadType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string { return "file_upload"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\FileUploadView::class,
        ];
    }

    protected function extraOptionsAfterBasic(): array {
        return [
            'image' => FastTypeOption::make(false,
                Toggle::make('image')
                    ->afterStateUpdated(function($state, $set){
                        if($state) return;
                        $set('show_images', false);
                        $set('show_images_in_view', false);
                    })
                    ->label("Nur Bilder") //ToDo Translate
                    ->live()
            ),
            'show_images' => FastTypeOption::make(false,
                Toggle::make('show_images')
                    ->columnStart(1)
                    ->label("Bilder Anzeigen") //ToDo Translate
                    ->disabled(fn($get) => !$get('image'))
            ),
            'show_images_in_view' => FastTypeOption::make(false,
                Toggle::make('show_images_in_view')
                    ->label("Bilder Anzeigen in Ansicht") //ToDo Translate
                    ->disabled(fn($get) => !$get('image'))
            ),
            'downloadable' => FastTypeOption::make(false,
                Toggle::make('downloadable')
                    ->label("Herunterladbar") //ToDo Translate
            ),
            'multiple' => FastTypeOption::make(false,
                Toggle::make('multiple')
                    ->label("mehrere hochladbar") //ToDo Translate
            ),
            'preserve_filenames' => FastTypeOption::make(true,
                Toggle::make('preserve_filenames')
                    ->label("Ursprungsname speichern") //ToDo Translate
            ),
        ];
    }


    public function icon(): String {
        return  "carbon-copy-file";
    }



}
