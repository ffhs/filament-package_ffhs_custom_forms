<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\ImageTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowLabelOption;
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
                        ->label('Bild') //ToDo Translate
                        ->visibility('private')
                        ->columnSpanFull()
                        ->downloadable()
                        ->previewable()
                        ->image()
                        ->live()
                ),
            ], 'carbon-data-1'),
            LayoutOptionGroup::make()
                ->removeTypeOption('inline_label')
                ->mergeTypeOptions([
                    'column_span' => new ColumnSpanOption(),
                    'width' => FastTypeOption::makeFast(
                        null,
                        static fn($name) => TextInput::make($name)
                            ->label('Breite') //ToDo Translate
                            ->minValue(1)
                            ->numeric()
                    ),
                    'show_label' => (ShowLabelOption::make())->modifyDefault(fn($default) => false),
                    'show_in_view' => (ShowInViewOption::make())->modifyDefault(fn($default) => false),
                ]),

        ];
    }
}
