<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFrontLayoutSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Lang;

class TextType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    use HasFrontLayoutSettings;

    public static function identifier(): string {return "text";}

    public function viewModes(): array {
        return [
          'default' => TextTypeView::class
        ];
    }

    public function icon(): string {
       return "bi-input-cursor-text";
    }

    protected function extraOptionsBeforeBasic(): array {
        return [
            'alpine_mask' => new AlpineMaskOption(),
            'max_length' => new MaxLengthOption(),
            'min_length' => new MinLengthOption(),
        ];
    }

    protected function extraOptionsAfterBasic(): array {
        return [
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
        ];
    }

}
