<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FlattedNested;

use Ffhs\FilamentPackageFfhsCustomForms\Identifiers\HasIdentifier;
interface NestingObject extends HasIdentifier
{
    public static function getPositionAttribute(): string;
    public static function getEndContainerPositionAttribute(): string;

}
