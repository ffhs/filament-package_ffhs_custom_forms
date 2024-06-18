<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\Views\TitleTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ShowInViewOption;
use Filament\Forms\Components\TextInput;

class TitleType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function identifier(): string {
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
