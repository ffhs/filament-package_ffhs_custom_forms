<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Closure;
use Illuminate\Support\Collection;


interface CachedModel
{

    public static function getCacheDuration(): mixed;

    public static function allCached();

    public static function singleListCached();

    public static function addToCachedList(Collection|CachedModel $toAdd);


    public static function cached(mixed $value, string $attribute = "id", bool $searching = true);

    public static function cachedMultiple(string $attribute , bool $searching , mixed... $values);


    public function getRelationCacheName(string $relationName);

    public function setValueInManyRelationCache(string $relationName, mixed $value);

    public function caching(bool|Closure $disable = true, bool|null|Closure $recursive = false):static;

    public function isCaching():bool;
    public function isRecursiveCaching():bool|null;
    public function getDefaultCaching():bool|Closure;

}
