<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\Views\KeyValueTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\HasCustomTypePackageTranslation;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups\DefaultLayoutTypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\FastTypeOption;
use Filament\Forms\Components\Toggle;

class KeyValueType extends CustomOptionType
{
    use HasCustomTypePackageTranslation;
    public static function identifier(): string { return "key_value"; }

    public function viewModes(): array {
        return  [
            'default'  => KeyValueTypeView::class,
        ];
    }

    public function icon(): String {
        return  "heroicon-o-key";
    }

    protected function extraOptionsAfterBasic(): array {
        return [
            DefaultLayoutTypeOptionGroup::make(),
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
