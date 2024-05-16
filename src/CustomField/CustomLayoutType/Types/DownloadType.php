<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\DownloadTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowTitleOption;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;

class DownloadType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {
        return "download_file";
    }

    public function viewModes(): array {
        return [
          'default' => DownloadTypeView::class
        ];
    }

    public function icon(): string {
       return "tabler-download";
    }

    public function getExtraTypeOptions(): array {
        return [
            'file_names' => new FastTypeOption([], Group::make()),
            'files'=> new FastTypeOption([],
                FileUpload::make('files')
                    ->afterStateUpdated(fn($set, $state) => sizeof($state) > 1? $set("title_as_filename", false):null)
                    ->directory(config("ffhs_custom_forms.field_settings.download_file.save_path"))
                    ->storeFileNamesIn('file_names')
                    ->label("Datei/-en") //ToDo Translate
                    ->visibility('private')
                    ->disk('local')
                    ->columnSpanFull()
                    ->multiple()
                    ->live()
            ),

            'column_span' => new ColumnSpanOption(),
            'new_line_option' => new NewLineOption(),

            'show_in_view'=> new ShowInViewOption(),
            'show_title'=> new ShowTitleOption(),
            'show_as_link'=> new FastTypeOption(true,
                Toggle::make("show_as_link")
                    ->label("Link") //ToDo Translate
            ),
            'title_as_filename'=> new FastTypeOption(false,
                Toggle::make("title_as_filename")
                    ->disabled(fn($get) => sizeof($get('files')?? []) > 1)
                    ->label("Titel als Filename") //ToDo Translate
            )
        ];
    }


    public function canBeRequired(): bool {
        return false;
    }

}
