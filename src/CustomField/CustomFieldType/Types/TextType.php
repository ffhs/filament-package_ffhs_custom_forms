<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use DanHarrin\LivewireRateLimiting\Tests\Component;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\AlpineMaskOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLenghtOption;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Lang;

class TextType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "text";}

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
            'max_length' => new MaxLengthOption(),
            'min_length' => new MinLenghtOption(),
            'alpine_mask' => new AlpineMaskOption(),
        ];
    }

    protected function extraOptionsAfterBasic(): array {
        return [
            'suggestions' => new FastTypeOption([],
                Section::make("Vorschläge")
                    ->collapsed()
                    ->schema([
                        Repeater::make('suggestions')
                            ->addActionLabel("Vorschlag hinzufügen")
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
