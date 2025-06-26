<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Exceptions;

class FieldDataHasNoOrWrongCustomFieldTypeException extends \RuntimeException
{
    public function __construct(string|array $message)
    {
        if (is_array($message)) {
            $rawType = $message['type'] ?? '';
            $message = $message['name'][app()->getLocale()] ?? 'undefined';
            $message .= empty($rawType) ? ' has no custom field type' : ' has an invalid custom field type: ' . $rawType;
        }
        parent::__construct($message);
    }
}
