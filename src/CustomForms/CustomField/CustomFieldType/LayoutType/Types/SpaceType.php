<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\SpaceTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\TextInput;

class SpaceType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "space";
    }

    public function viewModes(): array
    {
        return [
            "default" => SpaceTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "carbon-name-space";
    }

    public function hasToolTips(): bool
    {
        return false;
    }

    public function hasEditorNameElement(array $fielData): bool
    {
        return false;
    }


    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make()
                ->removeTypeOption('new_line')
                ->setTypeOptions([
                    'amount' => new FastTypeOption(
                        1,
                        TextInput::make("amount")
                            ->label("Grösse")
                            ->columnStart(1)
                            ->minValue(1)
                            ->required()
                            ->numeric()
                    ),
                    'show_in_view' => ShowInViewOption::make(),
                ]),
        ];
    }

}
