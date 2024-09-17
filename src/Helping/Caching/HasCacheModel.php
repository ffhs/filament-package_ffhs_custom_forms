<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use function Pest\Laravel\instance;

trait HasCacheModel
{

    /**
     * "relation" => ["local_key, "key"]
     *IMPORTANT: $cachedRelations for 1:1
     *  protected array $cachedBelongsTo = [];
     *
     * "relation name"
     * protected static array $cacheWith = [];
     *  protected static bool $defaultCaching = false; <= to Default disabling Caching
     *
     * IMPORTANT: $cachedRelation for all but not 1:1
     * if a method exist named cachedRelationName($cacheKey)) than it use this
     * protected array $cachedRelation = [];
     *
     * protected array $defaultCaching
     *  protected array $cachedResults = [];
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

        if(!empty($this->getCachedBelongsTo()[$key]))  {
            if($key == "type")Debugbar::info("WTF");
            if(parent::relationLoaded($key)) return parent::__get($key);
            $relation = $this->getBelongsToCached($key);
            $this->relations[$key] = $relation;
            return $relation;
        }

        if(in_array($key, $this->getCachedRelations())){
            if(parent::relationLoaded($key)) return parent::__get($key);
            $result = $this->getRelationCached($key);

            if($result instanceof RelationCachedInformations) $result = $result->getModels();
            $this->relations[$key] = $result;

            return $result;
        }
        if(in_array($key, $this->getCachedResults())) return $this->getResultCached($key);


        return parent::__get($key);
    }

    public function setCacheValue(string $key, mixed $value): void {
        if(!$this->isCaching()) return;
        Cache::set($this->getCacheKeyForAttribute($key), $value, static::getCacheDuration());
    }


    public static function allCached(): Collection{
        return Cache::remember(
            (new static())->getTable()."-all",
            self::getCacheDuration(),
            function() {
                $all = static::all();
                static::addToModelCache($all);

                return new RelationCachedInformations(static::class, $all->pluck('id')->toArray());
            }
        )->getModels();
    }

    public static function getModelCache(): Collection{
        return Cache::get(static::getModelCacheKey()) ?? collect();
    }


    public static function clearModelCache(): void {
        Cache::forget(static::getModelCacheKey());
    }

    protected static function getModelCacheKey(): string {
        return (new static())->getTable(). "_cached_list";
    }

    public static function addToModelCache(Collection|CachedModel $toAdd): Collection|CachedModel {
        $cachedList = static::getModelCache();
        if(is_null($cachedList)) $cachedList = collect();
        if($toAdd instanceof Collection) $cachedList =
            $cachedList->merge($toAdd->whereNotIn("id",$cachedList->pluck("id")));
        else if(!in_array($toAdd->id, $cachedList->pluck("id")->toArray())) $cachedList = $cachedList->add($toAdd);

        Cache::set(static::getModelCacheKey(), $cachedList, self::getCacheDuration());

        return $toAdd;
    }


    public static function cached(mixed $value, string $attribute = "id", array $with = []): ?static{
        $output = static::getModelCache()?->where($attribute, $value)->first();
        if(!is_null($output)) return $output;
        if(is_null($value)) return $output;

        //if(!$searching) return $output;
        $output = static::query()->where($attribute, $value)->with(array_merge(static::getCacheWith(), $with))->first();
        if(is_null($output)) return null;
        static::addToModelCache($output);
        return $output;
    }



    public function cachedClear(string $key){
        Cache::forget($this->getCacheKeyForAttribute($key));
        unset($this->relations[$key]);
    }



    public function getCacheKeyForAttribute(string $relationName): string {
        return $this->getTable()."-".$relationName."-". $this->id;
    }




    public function getCachedResults():array{
        return get_object_vars($this)["cachedResults"] ?? [];
    }
    public function getCachedBelongsTo():array{
        return [];
    }
    public function getCachedRelations():array
    {
        $belongsTo = array_keys(get_object_vars($this)["cachedBelongsTo"] ?? []);
        $many = get_object_vars($this)["cachedRelations"] ?? [];
        return array_merge($belongsTo, $many);
    }



    public static function getCacheWith():array{
        if(!property_exists(static::class, 'cacheWith')) return [];
        return static::$cacheWith ?? [];
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






    public function getResultCached($name): mixed{
        $cacheKey = $this->getCacheKeyForAttribute($name);
        if(Cache::has($cacheKey)) return Cache::get($cacheKey);
        $result = $this->$name();
        Cache::set($cacheKey, $result, static::getCacheDuration());
        return $result;
    }

    public function getRelationCached($name): mixed{
        Debugbar::info($name);
        $cacheKey = $this->getCacheKeyForAttribute($name);
        if(Cache::has($cacheKey)) return Cache::get($cacheKey);

        //rune cachedAttribute method
        $cacheKey = $this::getCacheKeyForAttribute($name);
        $cacheMethodeName = "cached" . ucfirst($name);

        if(method_exists($this, $cacheMethodeName)) return $this->$cacheMethodeName();

        //Belongs to
        $relation = $this->$name();
        if ($relation instanceof BelongsTo) {
            $related = $relation->getRelated();
            $ownerKey = $relation->getOwnerKeyName();
            $foreignKey = $relation->getForeignKeyName();
            return $related::cached($this->$foreignKey, $ownerKey);
        }

        //Many RelationShips
        return Cache::remember($cacheKey, static::getCacheDuration(), function () use ($name) {
            /**@var Relation $relation */
            $relation = $this->$name();
            $result = $relation->get();
            $related = $relation->getRelated();
            $collectionOutput = !($relation instanceof HasOne);

            if(!($related instanceof CachedModel)) return $result;

            /**@var CachedModel $related */
            $related::addToModelCache($result);
            return new RelationCachedInformations($related::class, $result->pluck("id")->toArray(), $collectionOutput);
        });
    }

    public function getBelongsToCached($name): mixed{
        $relationData = $this->getCachedBelongsTo()[$name];
        $localKey = $relationData[0];
        $relatedKey = $relationData[1] ?? "id";

        /**@var Relation $relation */
        $relation = $this->$name();
        $cache = $relation->getRelated()::cached($this->$localKey, $relatedKey);

        $this->relations[$name] = $cache;

        return $cache;
    }

    public function isPropertyCached($name): bool{
        if(in_array($name, $this->getCachedResults()) || in_array($name, $this->getCachedRelations())){
            $cacheKey = $this->getCacheKeyForAttribute($name);
            return Cache::has($cacheKey);
        }

        if(array_key_exists($name, $this->getCachedBelongsTo())){


            $relationData = $this->getCachedBelongsTo()[$name];
            $localKey = $relationData[0];
            $relatedKey = $relationData[1] ?? "id";

            /**@var Relation $relation */
            $relation = $this->$name();
            $related = $relation->getRelated();

            $test = $related::getModelCache()->where($relatedKey,$this->$localKey)->count() === 1;
            Debugbar::info($name . " B");
            Debugbar::info($related::getModelCache()->where($relatedKey,$this->$localKey)->count());
            if($test) Debugbar::info($this->getBelongsToCached($name));

            return $test;
        }

        return false;
    }



    public function relationLoaded($key):bool
    {
        if(parent::relationLoaded($key)) return true;
        return$this->isPropertyCached($key);
    }

    public function getRelationValue($key)
    {
        if(parent::relationLoaded($key)) return parent::getRelationValue($key);
        if($this->isPropertyCached($key)) return $this->__get($key);
        return parent::getRelationValue($key);

    }


}
