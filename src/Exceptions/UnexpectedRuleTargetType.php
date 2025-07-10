<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Exceptions;

use Exception;
use Throwable;

class UnexpectedRuleTargetType extends Exception
{
    public function __construct(
        array $allowedTypes,
        string $actualType,
        string $class,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            'Only allowed [' . implode(',', $allowedTypes)
            . '] as target but get ' . $actualType . ' in ' . $class,
            $code,
            $previous
        );
    }
}
