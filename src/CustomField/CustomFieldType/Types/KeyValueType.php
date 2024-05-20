<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasBasicSettings;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomFormPackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\Types;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\Toggle;

class KeyValueType extends CustomOptionType
{
    use HasCustomFormPackageTranslation;
    use HasBasicSettings;
    public static function getFieldIdentifier(): string { return "key_value"; }

    public function viewModes(): array {
        return  [
            'default'  => Types\Views\KeyValueTypeView::class,
        ];
    }

    public function icon(): String {
        return  "heroicon-o-key";
    }

    protected function extraOptionsAfterBasic(): array {
        return [
          'reorderable' => new FastTypeOption(false,
              Toggle::make('reorderable')
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.reorderable"))
                ->columnSpanFull()
          ),
          'editableKeys' => new FastTypeOption(true,
            Toggle::make('editableKeys')
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.editable_keys"))
                ->columnSpanFull()
          ),
          'editableValues' => new FastTypeOption(true,
              Toggle::make('editableValues')
                  ->label(__("filament-package_ffhs_custom_forms::custom_forms.fields.type_options.editable_values"))
                  ->columnSpanFull()
          )
        ];
    }
}
