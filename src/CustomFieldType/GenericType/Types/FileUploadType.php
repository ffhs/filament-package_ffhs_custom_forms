<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\FileUploadView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ReorderableTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class FileUploadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'file_upload';
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->addTypeOptions(
                    'grid_layout',
                    FastTypeOption::makeFast(
                        false,
                        Toggle::make('grid_layout')
                            ->helperText(TypeOption::__('grid_layout.helper_text'))
                            ->label(TypeOption::__('grid_layout.label'))
                            ->hidden(fn($get) => !$get('image'))
                    )
                ),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'image' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('image')
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
                    'show_images' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('show_images')
                            ->label(TypeOption::__('show_images.label'))
                            ->helperText(TypeOption::__('show_images.helper_text'))
                            ->disabled(fn($get) => !$get('image'))
                            ->hidden(fn($get) => !$get('image'))
                            ->live()
                    ),
                    'show_images_in_view' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('show_images_in_view')
                            ->label(TypeOption::__('show_images_in_view.label'))
                            ->helperText(TypeOption::__('show_images_in_view.helper_text'))
                            ->disabled(fn($get) => !$get('image'))
                            ->hidden(fn($get) => !$get('image'))
                            ->live()
                    ),
                    'downloadable' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('downloadable')
                            ->label(TypeOption::__('downloadable.label'))
                            ->helperText(TypeOption::__('downloadable.helper_text'))
                    ),
                    'multiple' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('multiple')
                            ->label(TypeOption::__('multiple_uploads_allowed.label'))
                            ->helperText(TypeOption::__('multiple_uploads_allowed.helper_text'))
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    return;
                                }
                                $set('reorderable', false);
                            })
                            ->live()
                    ),
                    'reorderable' => ReorderableTypeOption::make()
                        ->modifyOptionComponent(function (Toggle $component) {
                            return $component->hidden(fn($get) => !$get('multiple'));
                        }),
                    'preserve_filenames' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('preserve_filenames')
                            ->label(TypeOption::__('preserve_filenames.label'))
                            ->helperText(TypeOption::__('preserve_filenames.helper_text'))
                    ),
                    'open_in_new_tab' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('open_in_new_tab')
                            ->label(TypeOption::__('open_in_new_tab.label'))
                            ->helperText(TypeOption::__('open_in_new_tab.helper_text'))
                            ->hidden(fn($get) => $get('image'))
                    ),
                    'allowed_type' => FastTypeOption::makeFast(
                        [
                            'application/pdf',
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        TagsInput::make('allowed_type')
                            ->columnSpanFull()
                            ->label(TypeOption::__('allowed_file_types.label'))
                            ->helperText(TypeOption::__('allowed_file_types.helper_text'))
                    ),
                ]),

        ];
    }

    public function icon(): string
    {
        return 'carbon-copy-file';
    }

    public function updateAnswerFormComponentOnSave(
        Component $component,
        CustomField $customField,
        Form $form,
        Collection $flattenFormComponents
    ): void {
        try {
            $acceptedFileTypes = $component->getAcceptedFileTypes();
            foreach (Arr::wrap($component->getState()) as $key => $file) {
                if (!$file instanceof TemporaryUploadedFile) {
                    continue;
                }

                $mimeType = $file->getMimeType();

                // Do not save if even one of the submitted files mimetype does not match the accepted file types
                if (!in_array($mimeType, $acceptedFileTypes, true)) {
                    $component->deleteUploadedFile($key);
                }
            }
            $state = array_filter($component->getState() ?? [], static fn($file) => !empty($file));
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

    public function prepareToSaveAnswerData(CustomFieldAnswer $answer, mixed $data): array
    {
        foreach ($data['files'] as $key => $file) {
            if (is_array($file)) {
                unset($data['files'][$key]);
            }
        }
        return parent::prepareToSaveAnswerData($answer, $data);
    }

    public function prepareLoadAnswerData(CustomFieldAnswer $answer, ?array $data): mixed
    {
        if (is_null($data)) {
            return null;
        }

        $data = parent::prepareLoadAnswerData($answer, $data);
        foreach ($data['files'] as $key => $file) {
            if (is_array($file)) {
                unset($data['files'][$key]);
            }
        }

        return $data;
    }
}
