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
                ->label("Kann umgeordnet werden") //ToDo Translate
                ->columnSpanFull()
          ),
          'editableKeys' => new FastTypeOption(true,
            Toggle::make('editableKeys')
                ->label("Bearbeitbare Schlüssel") //ToDo Translate
                ->columnSpanFull()
          ),
          'editableValues' => new FastTypeOption(true,
              Toggle::make('editableValues')
                  ->label("Bearbeitbare Werte") //ToDo Translate
                  ->columnSpanFull()
          )
        ];
    }


}
