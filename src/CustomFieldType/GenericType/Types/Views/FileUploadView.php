<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class FileUploadView implements FieldTypeView
{
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent {
        $fileUpload = FileUpload::make($this->getIdentifyKey($record) . '.files');

        return $this->prepareFileUploadComponent($fileUpload, $record);
    }

    public function prepareFileUploadComponent(FileUpload $component, CustomField $record): FileUpload
    {
        /** @var FileUpload $component */
        $component = $this->modifyFormComponent($component, $record);
        $component
            ->deleteUploadedFileUsing(fn(FileUpload $component) => $component->callAfterStateUpdated())
            ->multiple(fn($state) => $this->getOptionParameter($record, 'multiple'))
            ->acceptedFileTypes($this->getOptionParameter($record, 'allowed_type'))
            ->appendFiles()
            ->live();


        if ($this->getOptionParameter($record, 'image')) {
            $component = $component
                ->previewable($this->getOptionParameter($record, 'show_images'))
                ->downloadable($this->getOptionParameter($record, 'downloadable'))
                ->disk($this->getTypeConfigAttribute($record, 'images.disk'))
                ->directory($this->getTypeConfigAttribute($record, 'images.save_path'))
                ->image();
        } else {
            $component = $component
                ->directory($this->getTypeConfigAttribute($record, 'files.save_path'))
                ->disk($this->getTypeConfigAttribute($record, 'files.disk'))
                ->previewable(false);
        }

        if ($this->getOptionParameter($record, 'preserve_filenames')) {
            $component = $component->storeFileNamesIn($this->getIdentifyKey($record) . '.file_names');
        }

        if ($this->getOptionParameter($record, 'grid_layout')) {
            $component = $component->panelLayout('grid');
        }

        return $component;
    }


    public function getInfolistComponent(
        CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        $answer = $this->getAnswer($record);

        if (is_null($answer) || !isset($answer['file_names'])) {
            $names = [];
            $files = [];
        } elseif ($this->getOptionParameter($record, 'preserve_filenames')) {
            $names = $answer['file_names'];
            $files = array_values($answer['files']);
        } else {
            $names = $answer;
            $files = array_values($answer['files']);
        }

        //disk
        $image = $this->getOptionParameter($record, 'image');
        if ($image) {
            $disk = $this->getTypeConfigAttribute($record, 'images.disk');
        } else {
            $disk = $this->getTypeConfigAttribute($record, 'files.disk');
        }

        $diskRoot = config('filesystems.disks.' . $disk . '.root');

        if ($this->getOptionParameter($record, 'image')
            && $this->getOptionParameter($record, 'show_images_in_view')) {
            return $this->getInfolistImageComponent($files, $diskRoot, $record, $names);
        }

        return $this
            ->getInfoListFiles($files, $diskRoot, $record, $names)
            ->columnSpanFull();
    }


    public function getInfolistImageComponent(
        mixed $files,
        mixed $diskRoot,
        CustomFieldAnswer $record,
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
                        ->size(175),
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
        CustomFieldAnswer $record,
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

    public function getRedirectInfolistAction(CustomFieldAnswer $record, mixed $path, $urlPrefix): Action
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
    public function getTextEntryLabel(CustomFieldAnswer $record): TextEntry
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

    public function getInfoListFiles(
        mixed $files,
        mixed $diskRoot,
        CustomFieldAnswer $record,
        mixed $names
    ): Group {
        $downloadable = $this->getOptionParameter($record, 'downloadable');
        $openInNewTab = $this->getOptionParameter($record, 'open_in_new_tab');
        $urlPrefix = $this->getTypeConfigAttribute($record, 'files.url_prefix') ?? $diskRoot;
        $fileComponents = [];

        foreach ($files as $path) {
            if (!is_array($names)) {
                $names = [$path => $names];
            }

            $absolutePath = $diskRoot . '/' . $path;
            $actions = [];

            if ($downloadable) {
                $actions[] = $this->getDownloadInfolistAction($path, $record, $absolutePath, $names[$path]);
            }

            if ($openInNewTab) {
                $actions[] = $this->getRedirectInfolistAction($record, $path, $urlPrefix);
            }

            $fileComponents[] =
                View::make('filament-package_ffhs_custom_forms::filament.components.file-upload-display')
                    ->schema([Actions::make($actions)])
                    ->label($names[$path] ?? $path);
        }

        return Group::make([
            $this->getTextEntryLabel($record),
            Grid::make()
                ->schema($fileComponents)
                ->columns(),
        ]);
    }
}
