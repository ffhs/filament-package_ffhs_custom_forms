<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FlattedNestedList;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\NestingObject;

class NestedListElement implements NestingObject
{
    public int $nestingPosition = 1;
    public ?int $nestingEndPosition = null;

    public static function getPositionAttribute(): string
    {
        return 'nestingPosition';
    }

    public static function getEndContainerPositionAttribute(): string
    {
        return 'nestingEndPosition';
    }
}
