<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Groups;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\ColumnSpanOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\NewLineOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options\RequiredOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Filament\Forms\Components\Component;

class ValidationTypeOptionGroup extends TypeOptionGroup
{
    public static function make(string $name= "Validation", array $typeOptions = [], ?string $icon = 'carbon-scan-alt'): static { //ToDo translate
        if(empty($typeOptions)) {
            $typeOptions = [
                'required' => RequiredOption::make()
            ];
        }
        return parent::make($name, $typeOptions, $icon);
    }




}
