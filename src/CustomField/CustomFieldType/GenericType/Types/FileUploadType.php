<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\FileUploadView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use PHPUnit\Event\RuntimeException;

class FileUploadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "file_upload";
    }

    public function extraTypeOptions(): array
    {
        return [
            DefaultLayoutTypeOptionGroup::make()
                ->addTypeOptions(
                    "grid_layout",
                    FastTypeOption::makeFast(
                        false,
                        Toggle::make("grid_layout")
                            ->hidden(fn($get) => !$get('image'))
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.grid_layout")
                            )
                    )
                ),
            ValidationTypeOptionGroup::make()
                ->setTypeOptions([
                    'image' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('image')
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    return;
                                }
                                $set('show_images', false);
                                $set('show_images_in_view', false);
                                $set('grid_layout', false);
                            })
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.only_images")
                            )
                            ->live()
                    ),
                    'show_images' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('show_images')
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images")
                            )
                            ->disabled(fn($get) => !$get('image'))
                            ->hidden(fn($get) => !$get('image'))
                            ->live()
                    ),
                    'show_images_in_view' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('show_images_in_view')
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.show_images_in_view"
                                )
                            )
                            ->disabled(fn($get) => !$get('image'))
                            ->hidden(fn($get) => !$get('image'))
                            ->live()
                    ),
                    'downloadable' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('downloadable')
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.downloadable")
                            )
                    ),
                    'multiple' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('multiple')
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    return;
                                }
                                $set('reorderable', false);
                            })
                            ->live()
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.multiple_uploads_allowed"
                                )
                            )
                    ),
                    'reorderable' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('reorderable')
                            ->hidden(fn($get) => !$get('multiple'))
                            ->label(
                                __("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.reorderable")
                            )
                    ),
                    'preserve_filenames' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('preserve_filenames')
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.preserve_filenames"
                                )
                            )
                    ),

                    'open_in_new_tab' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('open_in_new_tab')
                            ->label(
                                __(
                                    "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.open_in_new_tab"
                                )
                            )
                            ->hidden(fn($get) => $get('image'))
                    ),

                    'allowed_type' => new FastTypeOption([
                        'application/pdf',
                        'image/jpeg',
                        'image/png'
                    ],
                        TagsInput::make("allowed_type")
                            ->columnSpanFull()
                            ->label("Erlaubte Typen") //ToDo translate
                    ),
                ]),

        ];
    }

    public function icon(): string
    {
        return "carbon-copy-file";
    }

    public function updateFormComponentOnSave(
        Component $component,
        CustomField $customField,
        Form $form,
        Collection $flattenFormComponents
    ): void {
        $componentKey = $customField->identifier . ".files";
        $filesComponents = $flattenFormComponents->filter(function (Component $component) use ($componentKey) {
            if (is_null($component->getKey())) {
                return false;
            }
            return str_contains($component->getKey(), $componentKey);
        });

        foreach ($filesComponents as $filesComponent) {
            $this->checkFileComponentTempData($filesComponent, $component);
        }
    }

    /**
     * @param FileUpload $filesComponent
     * @param Component $component
     * @return void
     */
    public function checkFileComponentTempData(FileUpload $filesComponent, Component $component): void
    {
        try {
            /**@var FileUpload $filesComponent */
            // $filesComponent = $this->viewModes()["default"]::prepareFileUploadComponent($filesComponent, $customField);

            // Check if the file mimetype matches one of the accepted file types

            $acceptedFileTypes = $filesComponent->getAcceptedFileTypes();
            $canSave = true;
            foreach (Arr::wrap($filesComponent->getState()) as $file) {
                if (!$file instanceof TemporaryUploadedFile) {
                    continue;
                }

                $mimeType = $file->getMimeType();

                // Do not save if even one of the submitted files mimetype does not match the accepted file types
                if (!in_array($mimeType, $acceptedFileTypes)) {
                    $canSave = false;
                    $file->delete();
                }
            }

            if ($canSave) {
                $filesComponent->saveUploadedFiles();
            } else {
                $component->state([]);
            }
        } catch (\Exception|RuntimeException $exception) {
            foreach (Arr::wrap($filesComponent->getState()) as $file) {
                $file->delete();
            }
        }
    }

    public function viewModes(): array
    {
        return [
            'default' => FileUploadView::class,
        ];
    }

    public function isEmptyAnswerer(CustomFieldAnswer $customFieldAnswer, ?array $fieldAnswererData): bool
    {
        return parent::isEmptyAnswerer(
                $customFieldAnswer,
                $fieldAnswererData
            ) || empty($fieldAnswererData["saved"]["files"]);
    }

    public function prepareSaveFieldData(CustomFieldAnswer $answer, mixed $data): array
    {
        $data = $data ?? ["files" => []];

        if (is_string($data["files"] ?? null)) {
            $data["files"] = [uniqid() => $data["files"]];
        }


        foreach ($data["files"] ?? [] as $key => $file) {
            if (is_array($file)) {
                unset($data["files"][$key]);
            }
        }
        return parent::prepareSaveFieldData($answer, $data);
    }

    public function prepareLoadFieldData(CustomFieldAnswer $answer, array $data): mixed
    {
        $data = parent::prepareLoadFieldData($answer, $data);
        $data = $data ?? ["files" => []];
        foreach ($data["files"] ?? [] as $key => $file) {
            if (is_array($file)) {
                unset($data["files"][$key]);
            }
        }
        return $data;
    }


}
