<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\FileUploadView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AllowedFileTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\Downloadable;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\GridLayoutOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MultipleOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\OpenInNewTabOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\PreserveFilenamesOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ReorderableTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowImagesOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class FileUploadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;
    use CanMapFields;

    public static function identifier(): string
    {
        return 'file_upload';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->addTypeOptions('grid_layout', GridLayoutOption::make()),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'image' => FastTypeOption::makeFast(
                        false,
                        fn($name) => Toggle::make($name)
                            ->label(TypeOption::__('only_images.label'))
                            ->helperText(TypeOption::__('only_images.helper_text'))
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    return;
                                }

                                $set('show_images', false);
                                $set('show_images_in_view', false);
                                $set('grid_layout', false);
                            })
                            ->live()
                    ),
                    'show_images' => ShowImagesOption::make(),
                    'show_images_in_view' => FastTypeOption::makeFast(
                        false,
                        fn($name) => Toggle::make($name)
                            ->label(TypeOption::__('show_images_in_view.label'))
                            ->helperText(TypeOption::__('show_images_in_view.helper_text'))
                            ->disabled(fn($get) => !$get('image'))
                            ->hidden(fn($get) => !$get('image'))
                            ->live()
                    ),
                    'downloadable' => Downloadable::make(),
                    'multiple' => MultipleOption::make(),
                    'reorderable' => ReorderableTypeOption::make()
                        ->modifyOptionComponent(
                            fn(Toggle $component) => $component->hidden(fn($get) => !$get('multiple'))
                        ),
                    'preserve_filenames' => PreserveFilenamesOption::make(),
                    'open_in_new_tab' => OpenInNewTabOption::make(),
                    'allowed_type' => AllowedFileTypeOption::make(),
                ]),
        ];
    }

    public function icon(): string
    {
        return 'carbon-copy-file';
    }

    public function updateAnswerFormComponentOnSave(
        Component|FileUpload $component,
        CustomField $customField,
        Schema $schema,
        Collection $flattenFormComponents
    ): void {
        if (!$component instanceof FileUpload) {
            throw new RuntimeException('Component is not a FileUpload');
        }

        try {
            $acceptedFileTypes = $component->getAcceptedFileTypes();
            $hasTemporaryFileToSaveInStorage = false;

            foreach (Arr::wrap($component->getState()) as $key => $file) {
                if (!$file instanceof TemporaryUploadedFile) {
                    continue;
                }
                $hasTemporaryFileToSaveInStorage = true;
                $mimeType = $file->getMimeType();

                // Do not save if even one of the submitted files mimetype does not match the accepted file types
                if (!in_array($mimeType, $acceptedFileTypes, true)) {
                    $component->deleteUploadedFile($key);
                    $file->delete();
                    return;
                }
            }

            //Prevents from looping because $component->saveUploadedFiles() calls an stateUpdated event, which can calls this method again
            if (!$hasTemporaryFileToSaveInStorage) {
                return;
            }

            $state = $component->getState();

            if (!is_array($state)) {
                $state = [$state];
            }

            $state = array_filter($state, static fn($file) => !empty($file));
            $component->state($state);
            $component->saveUploadedFiles();

        } catch (RuntimeException $exception) {
            foreach (Arr::wrap($component->getState()) as $file) {
                if ($file instanceof TemporaryUploadedFile && $file->exists()) {
                    $file->delete();
                }
            }
        }
    }

    public function viewModes(): array
    {
        return [
            'default' => FileUploadView::class,
        ];
    }

    public function isEmptyAnswer(CustomFieldAnswer $customFieldAnswer, ?array $fieldAnswererData): bool
    {
        if (parent::isEmptyAnswer($customFieldAnswer, $fieldAnswererData)) {
            return true;
        }

        return empty($fieldAnswererData['saved']['files']);
    }

    public function prepareToSaveAnswerData(EmbedCustomFieldAnswer $answer, mixed $data): array
    {
        foreach ($data['files'] as $key => $file) {
            if (is_array($file)) {
                unset($data['files'][$key]);
            }
        }

        return parent::prepareToSaveAnswerData($answer, $data);
    }

    public function prepareLoadAnswerData(EmbedCustomFieldAnswer $answer, ?array $data): mixed
    {
        if (is_null($data)) {
            return null;
        }

        $data = parent::prepareLoadAnswerData($answer, $data);

        if ($this->getOptionParameter($answer, 'multiple')) {
//            if (!is_array($data['files']) && !empty($data['files'])) {
//                $data['files'] = [$data['files']];
//            }
            foreach ($data['files'] as $key => $file) {
                if (is_array($file)) {
                    unset($data['files'][$key]);
                }
            }
        } elseif (is_array($data['files'])) {
            $data['files'] = array_values($data['files'])[0];
        }


        return $data;
    }
}
