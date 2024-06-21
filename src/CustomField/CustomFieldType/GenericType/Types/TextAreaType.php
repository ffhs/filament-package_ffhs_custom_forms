<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\TextAreaTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MaxLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\MinLengthOption;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasCustomFormPackageTranslation;
use Filament\Forms\Components\Toggle;

class TextAreaType extends CustomFieldType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;

    public static function identifier(): string {return "textarea";}


    public function viewModes(): array {
        return  [
          'default'=>   TextAreaTypeView::class
        ];
    }

    public function icon(): string {
        return  "bi-textarea-t";
    }

    protected function extraOptionsBeforeBasic(): array {

        $autoSizeComponent =
            Toggle::make("auto_size")
            ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.auto_size"))
            ->columnSpan(2);

        return [
            'max_length' => new MaxLengthOption(),
            'min_length' => new MinLengthOption(),
            'auto_size'=> new FastTypeOption(false,$autoSizeComponent),
        ];
    }


}
