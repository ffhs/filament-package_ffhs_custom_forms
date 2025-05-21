<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;

interface Type
{
    public static function identifier(): string;
    public static function make(): static;

    public static function getTypeFromIdentifier(string $identifier): ?static;

    public static function getTypeClassFromIdentifier(string $identifier): ?string;

    public static function getAllTypes(): ?array;

}
