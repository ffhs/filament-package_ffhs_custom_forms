<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\DownloadTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowAsLinkOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowLabelOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;

class DownloadType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'download_file';
    protected static string $icon = 'tabler-download';
    protected static array $viewModes = [
        'default' => DownloadTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            TypeOptionGroup::make('Data', [
                'file_names' => FastTypeOption::makeFast([], static fn($name) => Hidden::make($name)),
                'files' => FastTypeOption::makeFast([],
                    fn($name) => FileUpload::make($name)
                        ->afterStateUpdated(
                            fn($set, $state) => count($state) > 1 ? $set('title_as_filename', false) : null
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
                    'show_as_link' => ShowAsLinkOption::make(),
                    'title_as_filename' => FastTypeOption::makeFast(
                        false,
                        static fn($name) => Toggle::make($name)
                            ->disabled(fn($get) => count($get('files') ?? []) > 1)
                            ->label('Titel als Filename') //ToDo Translate
                    ),
                ]),
        ];
    }
}
