<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\CustomValidationAttributeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
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
            'default' => TextTypeView::class
        ];
    }

    public function icon(): string
    {
        return "bi-input-cursor-text";
    }

    public function extraTypeOptions(): array
    {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidationTypeOptionGroup::make(typeOptions: [
                'required' => RequiredOption::make(),
                'validation_attribute' => CustomValidationAttributeOption::make(),
                'alpine_mask' => AlpineMaskOption::make(),
                'max_length' => MaxLengthOption::make(),
                'min_length' => MinLengthOption::make(),
            ]),

            TypeOptionGroup::make('Vorschläge', [
                'suggestions' => new FastTypeOption([],
                    Group::make()
                        ->statePath('suggestions')
                        ->columnSpanFull()
                        ->schema(fn($record) => [
                            Repeater::make($record->getLocale())
                                ->addActionLabel(
                                    __(
                                        "filament-package_ffhs_custom_forms::custom_forms.fields.type_options.add_suggestion"
                                    )
                                )
                                ->label("")
                                ->schema([TextInput::make("value")->label("")])
                        ])
                ),
            ]), //ToDo Translate
        ];
    }

}
