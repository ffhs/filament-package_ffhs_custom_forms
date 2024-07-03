<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers\HasIdentifier;

interface NestingObject extends HasIdentifier
{
    public static function getPositionAttribute(): string;
    public static function getEndContainerPositionAttribute(): string;

}
