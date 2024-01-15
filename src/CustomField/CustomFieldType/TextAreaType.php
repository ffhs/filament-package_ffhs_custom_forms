<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;

class TextAreaType extends CustomFieldType
{
    //ToDo Verbessere

    use HasCustomFormPackageTranslation;

    public static function getFieldIdentifier(): string {return "textarea";}


    public function viewModes(): array {
        return  [
          'default'=>   Views\TextAreaTypeView::class
        ];
    }


}
