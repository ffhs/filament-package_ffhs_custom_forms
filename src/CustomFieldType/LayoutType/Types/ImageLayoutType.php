<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\ImageTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageLayoutType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'image_layout';
    protected static string $icon = 'bi-card-image';
    protected static array $viewModes = [
        'default' => ImageTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            TypeOptionGroup::make('Data', [
                'image' => FastTypeOption::makeFast(
                    [],
                    fn($name) => FileUpload::make($name)
                        ->directory($this->getConfigAttribute('save_path'))
                        ->disk($this->getConfigAttribute('disk'))
                        ->afterStateUpdated(function (FileUpload $component) {
                            if ($component->isSaved()) {
                                $component->saveUploadedFiles();
                            }
                        })
                        ->label(static::__('image'))
                        ->visibility($this->getConfigAttribute('visibility'))
                        ->columnSpanFull()
                        ->downloadable()
                        ->hiddenLabel()
                        ->previewable()
                        ->required()
                        ->image()
                        ->live(),
                ),
            ], 'carbon-data-1'),
            LayoutOptionGroup::make()
                ->removeTypeOption('inline_label')
                ->removeTypeOption('column_span')
                ->mergeTypeOptions([
                    'show_in_view' => (ShowInViewOption::make())->modifyDefault(fn($default) => false),
                    'column_span' => new ColumnSpanOption(),
                    'width' => FastTypeOption::makeFast(
                        null,
                        static fn($name) => TextInput::make($name)
                            ->label(static::__('width'))
                            ->helperText(static::__('width_helper'))
                            ->columnStart(1)
                            ->minValue(1)
                            ->nullable()
                            ->numeric()
                    ),
                    'height' => FastTypeOption::makeFast(
                        null,
                        static fn($name) => TextInput::make($name)
                            ->label(static::__('height'))
                            ->helperText(static::__('height_helper'))
                            ->minValue(1)
                            ->nullable()
                            ->numeric()
                    ),
                ]),

        ];
    }
}
