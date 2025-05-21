<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class TextType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return "text";
    }

    public function viewModes(): array
    {
        return [
            'default' => TextTypeView::class,
        ];
    }

    public function icon(): string
    {
        return "bi-input-cursor-text";
    }

    public function extraTypeOptions(): array
    {
        return [
            LayoutOptionGroup::make(),
            ValidationTypeOptionGroup::make()
                ->mergeTypeOptions([
                    'alpine_mask' => AlpineMaskOption::make(),
                    'max_length' => MaxLengthOption::make(),
                    'min_length' => MinLengthOption::make(),
                ]),

            TypeOptionGroup::make(TypeOption::__('suggestions.label'), [
                'suggestions' => FastTypeOption::makeFast([],
                    Group::make()
                        ->statePath('suggestions')
                        ->columnSpanFull()
                        ->schema(fn($record) => [
                            Repeater::make($record->getLocale())//ToDo maby make simple repeater
                            ->helperText(TypeOption::__('suggestions.helper_text'))
                                ->addActionLabel(TypeOption::__('suggestions.add_label'))
                                ->schema([TextInput::make("value")->label("")])
                                ->label(""),
                        ])
                ),
            ]),
        ];
    }

}
