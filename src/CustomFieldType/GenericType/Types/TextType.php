<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\LayoutOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class TextType extends CustomFieldType
{
    use HasCustomTypePackageTranslation;

    public static function identifier(): string
    {
        return 'text';
    }

    public function viewModes(): array
    {
        return [
            'default' => TextTypeView::class,
        ];
    }

    public function icon(): string
    {
        return 'bi-input-cursor-text';
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
                        ->schema(fn($record) => once(fn() => [
                            Repeater::make($record->getLocale()) //ToDo may make simple repeater
                            ->helperText(TypeOption::__('suggestions.helper_text'))
                                ->addActionLabel(TypeOption::__('suggestions.add_label'))
                                ->schema([TextInput::make('value')->label('')])
                                ->label(''),
                        ]))
                ),
            ]),
        ];
    }
}
