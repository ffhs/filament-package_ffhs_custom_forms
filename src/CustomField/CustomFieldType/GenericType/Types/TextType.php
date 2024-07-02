<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\ValidationTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Lang;

class TextType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    public static function identifier(): string {return "text";}

    public function viewModes(): array {
        return [
          'default' => TextTypeView::class
        ];
    }

    public function icon(): string {
       return "bi-input-cursor-text";
    }

    public function extraTypeOptions(): array {
        return [
            DefaultLayoutTypeOptionGroup::make(),
            ValidationTypeOptionGroup::make(typeOptions: [
                'required' => RequiredOption::make(),
                'alpine_mask' => new AlpineMaskOption(),
                'max_length' => new MaxLengthOption(),
                'min_length' => new MinLengthOption(),
            ]),
            TypeOptionGroup::make('Vorschläge', [
                'suggestions' => new FastTypeOption([],
                    Section::make(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.suggestions"))
                        ->collapsed()
                        ->schema([
                            Repeater::make('suggestions')
                                ->addActionLabel(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.add_suggestion"))
                                ->itemLabel(fn($state) => $state[Lang::locale()])
                                ->columnSpanFull()
                                ->collapsed()
                                ->columns()
                                ->label("")
                                ->reactive()
                                ->schema([
                                    TextInput::make("de")
                                        ->label("Deutsch")
                                        ->required(),
                                    TextInput::make("en")
                                        ->label("Englisch")
                                        ->required(),
                                ])
                        ])
                ),
            ]), //ToDo Translate
        ];
    }

}
