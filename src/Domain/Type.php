<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Domain;

abstract class Type
{

    public abstract static function identifier();

    public static function make(): static{
        return new static();
    }

    public abstract static function getConfigTypeList(): string;

    public static function getTypeListConfig():array {
        $path = static::getConfigTypeList();
        return config('ffhs_custom_forms.' . $path);
    }

    public static function getTypeFromIdentifier(string $identifier): ?static{
        $class = static::getTypeClassFromIdentifier($identifier);
        if(is_null($class)) return null;
        return static::getTypeClassFromIdentifier($identifier)::make();
    }

    public static function getTypeClassFromIdentifier(string $identifier): ?string{
        $allTypes = static::getTypeListConfig();
        return collect($allTypes)->firstWhere(fn($class)=>$class::identifier() == $identifier);
    }

    public static function getAllTypes(): ?array{
        $allTypes = static::getTypeListConfig();
        $output = [];
        foreach ($allTypes as $type) {
            $output[$type::identifier()] = $type;
        }
        return $output;
    }

}
