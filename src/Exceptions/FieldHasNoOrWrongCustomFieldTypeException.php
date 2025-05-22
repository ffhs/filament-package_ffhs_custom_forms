<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Exceptions;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

class FieldHasNoOrWrongCustomFieldTypeException extends \Exception
{
    public function __construct(string|CustomField $message)
    {
        if ($message instanceof CustomField) {
            $rawType = $message->type;
            $message = $message->name ?? '';
            $message .= empty($rawType) ? ' has no custom field type' : ' has an invalid custom field type: ' . $rawType;
        }

        parent::__construct($message);
    }


}
