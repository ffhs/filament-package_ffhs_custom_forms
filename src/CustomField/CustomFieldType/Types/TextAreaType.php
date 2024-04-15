<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLenghtOption;
use Filament\Forms\Components\Toggle;

class TextAreaType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function getFieldIdentifier(): string {return "textarea";}


    public function viewModes(): array {
        return  [
          'default'=>   Types\Views\TextAreaTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-textarea-t";
    }

    protected function extraOptionsBeforeBasic(): array {

        $autoSizeComponent =
            Toggle::make("auto_size")
            ->label("Automatische Grösse") //ToDo Translate
            ->columnSpan(2);

        return [
            'max_length' => new MaxLengthOption(),
            'min_length' => new MinLenghtOption(),
            'auto_size'=> new FastTypeOption(false,$autoSizeComponent),
        ];
    }


}
