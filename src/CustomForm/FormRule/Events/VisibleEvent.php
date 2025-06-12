<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

class VisibleEvent extends IsPropertyOverwriteEvent
{
    public static function identifier(): string
    {
        return 'visible_event';
    }

    protected function property(): string
    {
        return 'isVisible';
    }

    protected function dominatingSide(): bool
    {
        return false;
    }
}
