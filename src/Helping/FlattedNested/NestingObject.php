<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers\Identifier;

interface NestingObject extends Identifier
{
    public static function getPositionAttribute(): string;
    public static function getEndContainerPositionAttribute(): string;

}
