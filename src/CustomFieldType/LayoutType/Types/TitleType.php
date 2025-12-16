<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\TitleTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TextInput;

class TitleType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    protected static string $identifier = 'title';
    protected static string $icon = 'bi-card-heading';
    protected static array $viewModes = [
        'default' => TitleTypeView::class,
    ];

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->addTypeOptions(
                    'title_size',
                    FastTypeOption::makeFast(
                        1,
                        static fn($name) => TextInput::make($name)
                            ->label(TypeOption::__('title_size.label'))
                            ->helperText(TypeOption::__('title_size.helper_text'))
                            ->numeric()
                            ->columnStart(1)
                            ->step(1)
                            ->minLength(1)
                            ->maxLength(3)
                            ->required()
                    ),
                )
                ->addTypeOptions(
                    'show_in_view',
                    ShowInViewOption::make()
                ),
        ];
    }
}
