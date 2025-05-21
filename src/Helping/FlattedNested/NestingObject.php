<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested;

interface NestingObject
{
    public static function getPositionAttribute(): string;
    public static function getEndContainerPositionAttribute(): string;
}
