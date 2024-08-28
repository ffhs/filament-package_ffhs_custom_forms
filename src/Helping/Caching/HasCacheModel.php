<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasCacheModel
{

    /**
     * "relation" => ["local_key, "key"]
     *IMPORTANT: $cachedRelations for 1:1
     *protected array $cachedRelations = [];
     *
     * "relation name"
     * protected static array $cacheWith = [];
     *  protected static bool $defaultCaching = false; <= to Default disabling Caching
     *
     * IMPORTANT: $cachedManyRelations for all but not 1:1
     * if a method exist named cachedRelationName($cacheKey)) than it use this
     * protected array $cachedManyRelations = [];
     *
     * **/

    private bool|Closure $useCache;
    private bool|null|Closure $useRecursiveCache = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setToDefaultCaching();
    }

    protected function setToDefaultCaching(): static{
        $this->useCache = $this->getDefaultCaching();
        return $this;
    }

    public static function getCacheDuration(): mixed {
        return config('ffhs_custom_forms.cache_duration');
    }

    public function __get($key) {
        if(!$this->isCaching()){
            $result = parent::__get($key);
            if(!($result instanceof CachedModel)) return $result;
            else if(is_null($this->isRecursiveCaching())) return $result;
            else return $result->caching(false,$this->isRecursiveCaching());
        }

        if(!empty($this->getCachedRelations()[$key]))  return $this->get1To1CachedRelation($key);
        if(in_array($key, $this->getCachedManyRelations())) return $this->getNToNCachedRelation($key);
        return parent::__get($key);
    }


    public static function allCached(): ?Collection{
        return Cache::remember(
            (new static())->getTable()."-all",
            self::getCacheDuration(),
            fn() => static::addToCachedList(static::all())
        );
    }

    public static function singleListCached(): ?Collection{
        return Cache::get(static::getSingedListCacheName());
    }


    public static function singleListCacheClear()
    {
        return Cache::set(static::getSingedListCacheName(), Collection::make());
    }

    protected static function getSingedListCacheName(): string {
        return (new static())->getTable(). "_cached_list";
    }

    public static function addToCachedList(Collection|CachedModel $toAdd): Collection|CachedModel {
        $cachedList = static::singleListCached();
        if(is_null($cachedList)) $cachedList = collect();
        if($toAdd instanceof Collection) $cachedList =
            $cachedList->merge($toAdd->whereNotIn("id",$cachedList->pluck("id")));
        else if(!in_array($toAdd->id, $cachedList->pluck("id")->toArray())) $cachedList = $cachedList->add($toAdd);

        Cache::set(static::getSingedListCacheName(), $cachedList, self::getCacheDuration());

        return $toAdd;
    }


    public static function cached(mixed $value, string $attribute = "id", array $with = []): ?static{
        $output = static::singleListCached()?->where($attribute, $value)->first();
        if(!is_null($output)) return $output;
        if(is_null($value)) return $output;

        //if(!$searching) return $output;
        $output = static::query()->where($attribute, $value)->with(array_merge(static::getCacheWith(), $with))->first();
        if(is_null($output)) return null;
        static::addToCachedList($output);
        return $output;
    }




   /* public static function cachedMultiple(string $attribute , bool $searching , mixed... $values): Collection{
        $output = Cache::get(static::getFromSingedListName())?->whereIn($attribute, $values);
        if(is_null($output)) $output = collect();
        /**@var Collection $output*//*
        if(!$searching) return $output;
        $notFound = collect($values)->filter(fn($value) => $output->whereIn($attribute, $value)->count() == 0)->flatten();
        if($notFound->count() == 0) return $output;
        $notFounds = static::query()->whereIn($attribute, $notFound)->with(static::getCacheWith())->get();
        static::addToCachedList($notFounds);
        return $output->merge($notFounds);
    }*/


    private function get1To1CachedRelation(string $key) {
        $relationData = $this->getCachedRelations()[$key];
        $localKey = $relationData[0];
        $relatedKey = $relationData[1];


        /**@var Relation $relation */
        $relation = $this->$key();
        return $relation->getRelated()::cached($this->$localKey, $relatedKey);
    }
    public function cacheMultiRelationClear(string $key){
        Cache::forget($this->getRelationCacheName($key));
    }

    private function getNToNCachedRelation(string $key): mixed {
        $cacheKey = $this::getRelationCacheName($key);
        $cacheMethodeName = "cached" . ucfirst($key);

        if(method_exists($this, $cacheMethodeName)) return $this->$cacheMethodeName();

        return Cache::remember($cacheKey, static::getCacheDuration(), function () use ($key) {
            /**@var Relation $relation */
            $relation = $this->$key();
            $result = $relation->get();

            if($relation->getRelated() instanceof CachedModel)
                $relation->getRelated()::addToCachedList($result);

            return $result;
        });
    }

    public function getRelationCacheName(string $relationName): string {
        return (new static())->getTable()."-".$relationName."-".$this->id;
    }

    public function setValueInManyRelationCache(string $relationName, mixed $value): string {
        $cacheKey = $this::getRelationCacheName($relationName);
        return Cache::set($cacheKey, $value, static::getCacheDuration());
    }


    public function getCachedRelations():array
    {
        return $this->cachedRelations ?? [];
    }
    public static function getCacheWith():array
    {
        if(!property_exists(static::class, 'cacheWith')) return [];
        return static::$cacheWith ?? [];
    }

    public function getCachedManyRelations():array
    {
        return $this->cachedManyRelations ?? [];
    }


    public function caching(bool|Closure $useCache = true, bool|null|Closure $useRecursiveCache = null):static{
         $this->useCache = $useCache;
         $this->useRecursiveCache = $useRecursiveCache;
         return $this;
    }

    public function isCaching():bool{
        if(!isset($this->useCache))  $this->setToDefaultCaching();
        if($this->useCache instanceof Closure) return ($this->useCache)($this);
        return $this->useCache;
    }
    public function isRecursiveCaching():bool|null{
        if($this->useRecursiveCache instanceof Closure) return ($this->useRecursiveCache)($this);
        return $this->useRecursiveCache;
    }
    public function getDefaultCaching():bool{
        if(!property_exists(static::class, 'defaultCaching')) return true;
        return static::$defaultCaching ?? true;
    }


}
