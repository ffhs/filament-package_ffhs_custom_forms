<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types;


use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\Types\Views\SpaceTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Filament\Forms\Components\TextInput;

class SpaceType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'space';
    }

    public function viewModes(): array
    {
        return [
            'default' => SpaceTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'carbon-name-space';
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
                    'amount' => FastTypeOption::makeFast(
                        1,
                        TextInput::make('amount')
                            ->label(TypeOption::__('size.label'))
                            ->helperText(TypeOption::__('size.helper_text'))
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
