<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

interface NestingObject
{
    public static function getPositionAttribute(): string;

    public static function getEndContainerPositionAttribute(): string;
}
