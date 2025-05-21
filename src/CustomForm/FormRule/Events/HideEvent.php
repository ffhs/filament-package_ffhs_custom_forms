<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;


class HideEvent extends IsPropertyOverwriteEvent
{
    public static function identifier(): string
    {
        return "hidden_event";
    }

    protected function property(): string
    {
        return "isHidden";
    }

    protected function dominatingSide(): bool
    {
        return true;
    }
}
