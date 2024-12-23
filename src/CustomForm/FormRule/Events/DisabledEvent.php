<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

class DisabledEvent extends IsPropertyOverwriteEvent
{
    public static function identifier(): string {
        return "disabled_event";
    }
    protected function property(): string
    {
        return "isDisabled";
    }
    protected function dominatingSide(): bool
    {
        return false;
    }

}
