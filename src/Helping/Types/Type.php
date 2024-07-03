<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Types;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\Identifiers\StaticIdentifier;

interface Type extends StaticIdentifier
{

    public static function make(): static;

    public static function getTypeFromIdentifier(string $identifier): ?static;

    public static function getTypeClassFromIdentifier(string $identifier): ?string;

    public static function getAllTypes(): ?array;

}
