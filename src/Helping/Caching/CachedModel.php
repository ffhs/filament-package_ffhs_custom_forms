<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Closure;
use Illuminate\Support\Collection;


interface CachedModel
{

    public static function getCacheDuration(): mixed;




    public static function getModelCache(): Collection;
    public static function clearModelCache(): void;
    public static function addToModelCache(Collection|CachedModel $toAdd): void;
    public static function removeFromModelCache(array|Collection|int $toRemove): void;

    public static function allCached(): Collection;

    public static function cached(mixed $value, string $attribute = 'id', array $with = []);

    public function caching(bool|Closure $disable = true):static;

    public function isCaching():bool;

    public function getDefaultCaching():bool|Closure;

    public function getRelationCached($name): mixed;

    public function isPropertyCached($name): bool;



    public function getCachedRelations(): array;
    public function getCachedResults(): array;



    public function cachedClear(string $string);

    public function getCacheKeyForAttribute(string $relationName);

    public function setCacheValue(string $relationName, mixed $value);


}
