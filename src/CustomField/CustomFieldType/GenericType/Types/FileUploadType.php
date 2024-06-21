<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\FileUploadView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Collection;

class FileUploadType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function identifier(): string { return "file_upload"; }

    public function viewModes(): array {
        return  [
            'default'  => FileUploadView::class,
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
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.only_images"))
                    ->live()
            ),
            'show_images' => FastTypeOption::make(false,
                Toggle::make('show_images')
                    ->columnStart(1)
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images"))
                    ->disabled(fn($get) => !$get('image'))
            ),
            'show_images_in_view' => FastTypeOption::make(false,
                Toggle::make('show_images_in_view')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images_in_view"))
                    ->disabled(fn($get) => !$get('image'))
            ),
            'downloadable' => FastTypeOption::make(false,
                Toggle::make('downloadable')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.downloadable"))
            ),
            'multiple' => FastTypeOption::make(false,
                Toggle::make('multiple')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.multiple_uploads_allowed"))
            ),
            'preserve_filenames' => FastTypeOption::make(true,
                Toggle::make('preserve_filenames')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.preserve_filenames"))
            ),
        ];
    }


    public function icon(): String {
        return  "carbon-copy-file";
    }

    public function updateFormComponentOnSave(Component $component, CustomField $customField, Form $form, Collection $flattenFormComponents): void {
        $filesComponent = $flattenFormComponents->first(fn(Component $component) =>
            !is_null($component->getKey()) && str_contains($component->getKey(), $customField->identifier . ".files")
        );
        /**@var FileUpload $filesComponent*/
        $filesComponent = FileUploadView::prepareFileUploadComponent($filesComponent,$customField);
        $filesComponent->saveUploadedFiles();
    }

}
