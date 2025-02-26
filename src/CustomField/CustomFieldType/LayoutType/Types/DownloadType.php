<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\DownloadTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;

class DownloadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "download_file";
    }

    public function viewModes(): array
    {
        return [
            'default' => DownloadTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "tabler-download";
    }

    public function extraTypeOptions(): array
    {
        return [
            TypeOptionGroup::make("Data", [
                'file_names' => new FastTypeOption([], Hidden::make('file_names')),
                'files' => new FastTypeOption([],
                    FileUpload::make('files')
                        ->afterStateUpdated(
                            fn($set, $state) => sizeof($state) > 1 ? $set("title_as_filename", false) : null
                        )
                        ->directory($this->getConfigAttribute("save_path"))
                        ->disk($this->getConfigAttribute("disk"))
                        ->storeFileNamesIn('file_names')
                        ->label("Datei/-en") //ToDo Translate
                        ->visibility('private')
                        ->columnSpanFull()
                        ->downloadable()
                        ->previewable()
                        ->multiple()
                        ->live()
                ),
            ], "carbon-data-1"),
            
            LayoutOptionGroup::make()
                ->removeTypeOption('inline_label')
                ->mergeTypeOptions([
                    'show_in_view' => ShowInViewOption::make(),
                    'show_title' => ShowTitleOption::make(),
                    'show_as_link' => new FastTypeOption(
                        true,
                        Toggle::make("show_as_link")
                            ->label("Link") //ToDo Translate
                    ),
                    'title_as_filename' => new FastTypeOption(
                        false,
                        Toggle::make("title_as_filename")
                            ->disabled(fn($get) => sizeof($get('files') ?? []) > 1)
                            ->label("Titel als Filename") //ToDo Translate
                    ),
                ]),


        ];
    }
}
