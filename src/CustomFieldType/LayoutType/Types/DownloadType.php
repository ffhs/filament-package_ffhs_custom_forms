<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\DownloadTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;

class DownloadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'download_file';
    }

    public function viewModes(): array
    {
        return [
            'default' => DownloadTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'tabler-download';
    }

    public function extraTypeOptions(): array
    {
        return [
            TypeOptionGroup::make('Data', [
                'file_names' => FastTypeOption::makeFast([], Hidden::make('file_names')),
                'files' => FastTypeOption::makeFast([],
                    FileUpload::make('files')
                        ->afterStateUpdated(
                            fn($set, $state) => sizeof($state) > 1 ? $set('title_as_filename', false) : null
                        )
                        ->directory($this->getConfigAttribute('save_path'))
                        ->disk($this->getConfigAttribute('disk'))
                        ->storeFileNamesIn('file_names')
                        ->label('Datei/-en') //ToDo Translate
                        ->visibility('private')
                        ->columnSpanFull()
                        ->downloadable()
                        ->previewable()
                        ->multiple()
                        ->live()
                ),
            ], 'carbon-data-1'),

            LayoutOptionGroup::make()
                ->removeTypeOption('inline_label')
                ->mergeTypeOptions([
                    'show_in_view' => ShowInViewOption::make(),
                    'show_label' => ShowLabelOption::make(),
                    'show_as_link' => FastTypeOption::makeFast(
                        true,
                        Toggle::make('show_as_link')
                            ->label(TypeOption::__('show_as_link.label'))
                            ->helperText(TypeOption::__('show_as_link.helper_text'))
                    ),
                    'title_as_filename' => FastTypeOption::makeFast(
                        false,
                        Toggle::make('title_as_filename')
                            ->disabled(fn($get) => sizeof($get('files') ?? []) > 1)
                            ->label('Titel als Filename') //ToDo Translate
                    ),
                ]),
        ];
    }
}
