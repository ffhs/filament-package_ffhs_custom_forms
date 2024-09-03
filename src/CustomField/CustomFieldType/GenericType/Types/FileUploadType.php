<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\FileUploadView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileUploadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string { return "file_upload"; }

    public function viewModes(): array {
        return  [
            'default'  => FileUploadView::class,
        ];
    }

    public function extraTypeOptions(): array
    {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidationTypeOptionGroup::make()
            ->setTypeOptions([
                'image' => FastTypeOption::makeFast(false,
                    Toggle::make('image')
                        ->afterStateUpdated(function($state, $set){
                            if($state) return;
                            $set('show_images', false);
                            $set('show_images_in_view', false);
                        })
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.only_images"))
                        ->live()
                ),
                'show_images' => FastTypeOption::makeFast(false,
                    Toggle::make('show_images')
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images"))
                        ->disabled(fn($get) => !$get('image'))
                        ->hidden(fn($get) => !$get('image'))
                        ->live()
                ),
                'show_images_in_view' => FastTypeOption::makeFast(false,
                    Toggle::make('show_images_in_view')
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images_in_view"))
                        ->disabled(fn($get) => !$get('image'))
                        ->hidden(fn($get) => !$get('image'))
                        ->live()
                ),
                'downloadable' => FastTypeOption::makeFast(false,
                    Toggle::make('downloadable')
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.downloadable"))
                ),
                'multiple' => FastTypeOption::makeFast(false,
                    Toggle::make('multiple')
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.multiple_uploads_allowed"))
                ),
                'preserve_filenames' => FastTypeOption::makeFast(true,
                    Toggle::make('preserve_filenames')
                        ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.preserve_filenames"))
                ),

                'allowed_type' => new FastTypeOption([
                    'application/pdf',
                    'image/jpeg'
                ],
                    TagsInput::make("allowed_type")
                        ->columnSpanFull()
                        ->label("Erlaubte Typen") //ToDo translate
                ),
            ]),

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
