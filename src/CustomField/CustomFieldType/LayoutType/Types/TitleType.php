<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\TitleTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\TextInput;

class TitleType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "title";
    }

    public function viewModes(): array
    {
        return [
            'default' => TitleTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-card-heading";
    }

    public function extraTypeOptions(): array
    {
        return [
            DefaultLayoutTypeOptionGroup::make()
                ->removeTypeOption("helper_text")
                ->addTypeOptions(
                    'title_size',
                    FastTypeOption::makeFast(
                        1,
                        TextInput::make('title_size')
                            ->label('Title grösse')
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
