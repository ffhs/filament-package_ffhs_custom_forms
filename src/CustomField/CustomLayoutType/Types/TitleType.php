<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types\Views\TextTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\Types\Views\TitleTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\BooleanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class TitleType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {
        return "title";
    }

    public function viewModes(): array {
        return [
          'default' => TitleTypeView::class
        ];
    }

    public function icon(): string {
       return "bi-card-heading";
    }

    protected function extraOptionsBeforeBasic(): array {
        return [
            'title_size' => new FastTypeOption(1,
                TextInput::make("title_size")
                    ->label("Title grösse")
                    ->numeric()
                    ->columnStart(1)
                    ->step(1)
                    ->minLength(1)
                    ->maxLength(3)
                    ->required()
            ),
        ];
    }


    protected function extraOptionsAfterBasic(): array {
        return [
            'show_in_view'=> new ShowInViewOption()
        ];
    }

    public function canBeRequired(): bool {
        return false;
    }
    public function hasToolTips(): bool {
        return false;
    }

}
