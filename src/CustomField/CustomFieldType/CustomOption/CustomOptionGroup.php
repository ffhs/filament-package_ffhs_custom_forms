<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomOption;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
class CustomOptionGroup extends TypeOptionGroup
{
    public static function make(string $name= "Optionen", array $typeOptions = [], ?string $icon = 'heroicon-m-queue-list'): static { //ToDo translate
        return parent::make($name, $typeOptions, $icon);
    }


    public function __construct(string $name= "Optionen", array $typeOptions = [], ?string $icon = 'heroicon-m-queue-list') { //ToDo translate
        parent::__construct($name, $typeOptions, $icon);

        $this->mergeTypeOptions([
            "customOptions" => new CustomOptionTypeOption()
        ]);
    }


}
