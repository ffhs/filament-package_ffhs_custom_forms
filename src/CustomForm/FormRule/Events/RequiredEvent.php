<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

class RequiredEvent extends IsPropertyOverwriteEvent
{
    public static function identifier(): string
    {
        return "required_event";
    }

    protected function property(): string
    {
        return "isRequired";
    }

    protected function dominatingSide(): bool
    {
        return false;
    }


}
