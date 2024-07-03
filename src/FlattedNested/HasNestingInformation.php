<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FlattedNested;

use Ffhs\FilamentPackageFfhsCustomForms\Domain\HasFormIdentifier;
use Ffhs\FilamentPackageFfhsCustomForms\Identifiers\HasIdentifierParameter;

trait HasNestingInformation
{
    public int $nesting_position = 1;
    public ?int $nesting_end_position = null;

    public static function getPositionAttribute(): string{
        return 'nesting_position';
    }
    public static function getEndContainerPositionAttribute(): string{
        return 'nesting_end_position';
    }

}
