<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Rules\Exeptions;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

class FieldHasNoOrWrongCustomFieldTypeException extends \Exception
{
    public function __construct(string|CustomField $message)
    {
        if($message instanceof CustomField) {
            $rawType = $message->type;
            if(empty($rawType)) {
                $message = $message->name . ' has no custom field type';
            }else{
                $message = $message->name . ' has an invalid custom field type: ' . $rawType;
            }
        }

        parent::__construct($message);
    }


}
