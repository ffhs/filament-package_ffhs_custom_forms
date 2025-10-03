<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FileUploadTypeEntry;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Support\Components\Component;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class FileUploadView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
//        return FileUpload::make('files')
//            ->statePath($this->getIdentifyKey($customField) . '.files')
//            ->multiple();

        return $this->prepareFileUploadComponent(FileUpload::make('files'), $customField)
            ->statePath($this->getIdentifyKey($customField) . '.files');
    }

    public function prepareFileUploadComponent(FileUpload $component, EmbedCustomField $customField): FileUpload
    {
        $fileUpload = $this->modifyComponent($component, $customField, false);

        $fileUpload
            ->deleteUploadedFileUsing(fn(FileUpload $component) => $component->callAfterStateUpdated())
            ->acceptedFileTypes($this->getOptionParameter($customField, 'allowed_type'))
            ->multiple($this->getOptionParameter($customField, 'multiple'))
            ->openable(false)
            ->appendFiles()
            ->moveFiles()
            ->live();


        if ($this->getOptionParameter($customField, 'image')) {
            $fileUpload = $fileUpload
                ->previewable($this->getOptionParameter($customField, 'show_images'))
                ->downloadable($this->getOptionParameter($customField, 'downloadable'))
                ->disk($this->getTypeConfigAttribute($customField, 'images.disk'))
                ->directory($this->getTypeConfigAttribute($customField, 'images.save_path'))
                ->visibility($this->getTypeConfigAttribute($customField, 'images.visibility'))
                ->image();
        } else {
            $fileUpload = $fileUpload
                ->directory($this->getTypeConfigAttribute($customField, 'files.save_path'))
                ->disk($this->getTypeConfigAttribute($customField, 'files.disk'))
                ->visibility($this->getTypeConfigAttribute($customField, 'files.visibility'))
                ->previewable(false);
        }

        if ($this->getOptionParameter($customField, 'preserve_filenames')) {
            $fileUpload = $fileUpload->storeFileNamesIn($this->getIdentifyKey($customField) . '.file_names');
        }

        if ($this->getOptionParameter($customField, 'grid_layout')) {
            $fileUpload = $fileUpload->panelLayout('grid');
        }

        return $fileUpload;
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $answer = $this->getAnswer($customFieldAnswer);

        if (is_null($answer) || !isset($answer['file_names'])) {
            $names = [];
            $files = [];
        } elseif ($this->getOptionParameter($customFieldAnswer, 'preserve_filenames')) {
            $names = $answer['file_names'];
            if (!is_array($answer['files'])) {
                $answer['files'] = [$answer['files']];
            }
            $files = array_values($answer['files']);
        } else {
            $names = $answer;
            $files = array_values($answer['files']);
        }

        //disk
        $image = $this->getOptionParameter($customFieldAnswer, 'image');
        if ($image) {
            $disk = $this->getTypeConfigAttribute($customFieldAnswer, 'images.disk');
        } else {
            $disk = $this->getTypeConfigAttribute($customFieldAnswer, 'files.disk');
        }

        $diskRoot = config('filesystems.disks.' . $disk . '.root');

        if ($this->getOptionParameter($customFieldAnswer, 'image')
            && $this->getOptionParameter($customFieldAnswer, 'show_images_in_view')) {
            return $this->getInfolistImageComponent($files, $diskRoot, $customFieldAnswer, $names);
        }

        return $this
            ->getEntryListFiles($files, $diskRoot, $customFieldAnswer, $names)
            ->columnSpanFull();
    }


    public function getInfolistImageComponent(
        mixed $files,
        mixed $diskRoot,
        EmbedCustomFieldAnswer $record,
        mixed $names
    ): Group {
        $groups = [];

        foreach ($files as $path) {
            if (!is_array($names)) {
                $names = [$path => $names];
            }

            $urlPrefix = $this->getTypeConfigAttribute($record, 'images.url_prefix') ?? $diskRoot;
            $groups[] = Fieldset::make($names[$path])
                ->schema([
                    ImageEntry::make($path)
                        ->disk($this->getTypeConfigAttribute($record, 'images.disk'))
                        ->label('')
                        ->state($path)
                        ->imageSize(175),
                    Actions::make([
                        $this->getDownloadInfolistAction($record, $path, $diskRoot . '/' . $path, $names[$path]),
                        $this->getRedirectInfolistAction($record, $path, $urlPrefix),
                    ])
                        ->alignment(Alignment::Center)
                        ->visible($this->getOptionParameter($record, 'downloadable')),
                ])
                ->columnSpan(1)
                ->columns(1)
                ->statePath($this->getIdentifyKey($record));
        }


        return Group::make([
            $this->getTextEntryLabel($record),
            Grid::make()
                ->schema($groups)
                ->columns(5),
        ])
            ->columnSpanFull()
            ->columns(1);
    }

    public function getDownloadInfolistAction(
        mixed $path,
        EmbedCustomFieldAnswer $record,
        string $absolutePath,
        $names
    ): Action {
        return Action::make($path . '-' . $this->getIdentifyKey($record) . '-action-download')
            ->action(fn() => response()->download($absolutePath, $names))
            ->icon('tabler-download')
            ->label('Download')
            ->link()
            ->iconButton();
    }

    public function getRedirectInfolistAction(EmbedCustomFieldAnswer $record, mixed $path, $urlPrefix): Action
    {
        return Action::make($path . '-' . $this->getIdentifyKey($record) . '-action-view')
            ->action(function ($livewire) use ($path, $urlPrefix) {
                $url = Request::root() . $urlPrefix . '/' . $path;
                $livewire->js('window.open(\'' . $url . '\', \'_blank\');');
            })
            ->icon('bi-folder-symlink')
            ->label('Redirect')
            ->link()
            ->iconButton();
    }

    /**
     * @param CustomFieldAnswer $record
     *
     * @return TextEntry
     */
    public function getTextEntryLabel(EmbedCustomFieldAnswer $record): TextEntry
    {
        return TextEntry::make($this->getIdentifyKey($record) . '-title')
            ->label(
                new HtmlString(
                    '</span> <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" style="margin-bottom: -25px; margin-left: -12px;">' . $this->getLabelName(
                        $record
                    ) . '</span> <span>'
                )
            );
    }

    public function getEntryListFiles(
        mixed $files,
        mixed $diskRoot,
        EmbedCustomFieldAnswer $fieldAnswer,
        mixed $names
    ): Group {
        $downloadable = $this->getOptionParameter($fieldAnswer, 'downloadable');
        $openInNewTab = $this->getOptionParameter($fieldAnswer, 'open_in_new_tab');
        $urlPrefix = $this->getTypeConfigAttribute($fieldAnswer, 'files.url_prefix') ?? $diskRoot;
        $fileComponents = [];

        foreach ($files as $path) {
            if (!is_array($names)) {
                $names = [$path => $names];
            }

            $absolutePath = $diskRoot . '/' . $path;
            $actions = [];

            if ($downloadable) {
                $actions[] = $this->getDownloadInfolistAction($path, $fieldAnswer, $absolutePath, $names[$path]);
            }

            if ($openInNewTab) {
                $actions[] = $this->getRedirectInfolistAction($fieldAnswer, $path, $urlPrefix);
            }

            $fileComponents[] =
                FileUploadTypeEntry::make($path)
                    ->hiddenLabel()
                    ->label($names[$path] ?? $path)
                    ->schema([Actions::make($actions)]);
        }

        return Group::make([
            $this->getTextEntryLabel($fieldAnswer),
            Grid::make()
                ->schema($fileComponents)
                ->columns(),
        ]);
    }
}
