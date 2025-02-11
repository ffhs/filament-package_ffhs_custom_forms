<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasCacheModel
{

    /**
     * protected static array $cacheWith = [];
     *  protected static bool $defaultCaching = false; <= to Default disabling Caching
     *
     * IMPORTANT: $cachedRelation for all but not 1:1
     * if a method exist named cachedRelationName($cacheKey)) than it use this
     * protected array $cachedRelation = [];
     *
     * protected array $defaultCaching
     *  protected array $cachedResults = [];
     * **/

    private bool|Closure $useCache;

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

    public static function getCacheDuration(): mixed {
        return config('ffhs_custom_forms.cache_duration');
    }

    public static function addToModelCache(Collection|CachedModel $toAdd): void{
        $cachedList = static::getModelCache();
        if(!($toAdd instanceof Collection)) $toAdd = collect([$toAdd]);
        $cachedList = $cachedList->merge($toAdd->keyBy("id"))->keyBy("id");
        Cache::set(static::getModelCacheKey(), $cachedList, self::getCacheDuration());
    }

    public static function getModelCache(): Collection{
        return Cache::get(static::getModelCacheKey()) ?? collect()->keyBy("id");
    }

    protected static function getModelCacheKey(): string {
        return (new static())->getTable(). "_cached_list";
    }

    public static function clearModelCache(?array $ids = null): void {
        if(is_null($ids)) Cache::forget(static::getModelCacheKey());

        $cache = static::getModelCache()->where("id", $ids);
        Cache::set(static::getModelCacheKey(), $cache, static::getCacheDuration());
    }

    protected static function booted()
    {
        parent::booted();

        self::deleted(function($model){
            self::removeFromModelCache($model->id);
        });

        self::created(function($model){
            self::addToModelCache($model);
        });

        self::updated(function($model){
            self::addToModelCache($model);
        });
    }

    public static function removeFromModelCache(array|Collection|int $toRemove): void{
        $cachedList = static::getModelCache();
        if(is_array($toRemove)) $toRemove = collect($toRemove);
        else if(!($toRemove instanceof Collection)) $toRemove = collect([$toRemove]);
        $cachedList = $cachedList->forget($toRemove);
        Cache::set(static::getModelCacheKey(), $cachedList, self::getCacheDuration());
    }

    public function setCacheValue(string $key, mixed $value): void {
        if(!$this->isCaching()) return;
        Cache::set($this->getCacheKeyForAttribute($key), $value, static::getCacheDuration());

        if($value instanceof RelationCachedInformations) $value = $value->getModels(); //ToDo decide
        $this->relations[$key] = $value;
    }

    public function isCaching():bool{
        if(!isset($this->useCache))  $this->setToDefaultCaching();
        if($this->useCache instanceof Closure) return ($this->useCache)($this);
        return $this->useCache;
    }

    protected function setToDefaultCaching(): static{
        $this->useCache = $this->getDefaultCaching();
        return $this;
    }

    public function getDefaultCaching():bool{
        if(!property_exists(static::class, 'defaultCaching')) return true;
        return static::$defaultCaching ?? true;
    }

    public function getCacheKeyForAttribute(string $relationName): string {
        return $this->getTable()."-".$relationName."-". $this->id;
    }

    public function relationCacheClear(): void{
        $this->relations = [];
        foreach($this->getCachedRelations() as $key)
            Cache::forget($this->getCacheKeyForAttribute($key));
    }

    public function getCachedRelations():array
    {
        $belongsTo = array_keys(get_object_vars($this)["cachedBelongsTo"] ?? []);
        $many = get_object_vars($this)["cachedRelations"] ?? [];
        return array_merge($belongsTo, $many);
    }

    public function cachedClear(string $key): void{
        Cache::forget($this->getCacheKeyForAttribute($key));
        unset($this->relations[$key]);
    }

    public function caching(bool|Closure $useCache = true):static{
        $this->useCache = $useCache;
        return $this;
    }

    public function getRelationValue($key)
    {
        // I now it look not quit clean but trust me clean it up makes to big performance problems
        if(parent::relationLoaded($key))
            return parent::getRelationValue($key);
        if(in_array($key, $this->getCachedRelations()))
            return $this->getRelationCached($key);
        return parent::getRelationValue($key);
    }

//    public function relationLoaded($key):bool
//    {
//        if(parent::relationLoaded($key))return true;
//        if(in_array($key, $this->getCachedRelations()))return true;
//        return  $this->isPropertyCached($key);
//    }

    public function getRelationCached($name): mixed{
        $cacheMethodeName = "cached" . ucfirst($name);
        if(method_exists($this, $cacheMethodeName)) $result = $this->$cacheMethodeName();
        else {
            $relation = $this->$name();
            //Belongs to
            if ($relation instanceof BelongsTo) $result = $this->getCachedBelongsTo($relation);
            else if ($relation instanceof HasOne) $result = $this->getCachedHasOne($relation);
            //Many RelationShips
            else $result = $this->getOtherCachedRelation($name);
        }

        if($result instanceof RelationCachedInformations) $result = $result->getModels();
        $this->relations[$name] = $result;

        return $result;
    }

    protected function getCachedBelongsTo(BelongsTo $relation): ?Model
    {
        $related = $relation->getRelated();
        $ownerKey = $relation->getOwnerKeyName();
        $foreignKey = $relation->getForeignKeyName();
        return $related::cached($this->$foreignKey, $ownerKey);
    }



    public static function cached(mixed $value, string $attribute = "id", array $with = []): ?static
    {
        $output = static::getModelCache()?->firstWhere($attribute, $value);
        if(!is_null($output)) return $output;
        if(is_null($value)) return null;

        $output = static::query()->where($attribute, $value)->with(array_merge(static::getCacheWith(), $with))->first();
        if($output) static::addToModelCache($output);
        return $output;
    }

    public static function getCacheWith():array{
        if(!property_exists(static::class, 'cacheWith')) return [];
        return static::$cacheWith ?? [];
    }

    protected function getCachedHasOne(HasOne $relation): ?Model
    {
        $related = $relation->getRelated();
        $foreignKeyName =$relation->getForeignKeyName();
        $localKey = $relation->getLocalKeyName();
        return $related::cached($this->$localKey, $foreignKeyName);
    }

    protected function getOtherCachedRelation(string $name): mixed
    {
        $cacheKey = $this->getCacheKeyForAttribute($name);
        //if(Cache::has($cacheKey)) return Cache::get($cacheKey);

        return Cache::remember($cacheKey, static::getCacheDuration(), function () use ($name) {
            /**@var Relation $relation */
            $relation = $this->$name();
            $related = $relation->getRelated();
            $result = parent::getRelationValue($name);


            if (is_null($result) || !($related instanceof CachedModel)) return $result;
            /**@var CachedModel $related */

            $related::addToModelCache($result);

            if ($result instanceof CachedModel)
                return new RelationCachedInformations($related::class, [$result->id], false);
            else return new RelationCachedInformations($related::class, $result->pluck("id")->toArray());
        });
    }

    public function isPropertyCached($name): bool{ //Do need?

        if(!in_array($name, $this->getCachedResults()) && !in_array($name, $this->getCachedRelations())) return false;

        $relation = $this->$name();
        if(!$relation instanceof BelongsTo){
            $cacheKey = $this->getCacheKeyForAttribute($name);
            return Cache::has($cacheKey);
        }

        $related = $relation->getRelated();
        $ownerKey = $relation->getOwnerKeyName();
        $foreignKey = $relation->getForeignKeyName();

        return $related::getModelCache()->where($ownerKey,$this->$foreignKey)->count() === 1;
    }

    public function getCachedResults():array{
        return get_object_vars($this)["cachedResults"] ?? [];
    }

//    public function getResultCached($name): mixed{
//        $cacheKey = $this->getCacheKeyForAttribute($name);
//        if(Cache::has($cacheKey)) return Cache::get($cacheKey);
//        $result = $this->$name();
//        Cache::set($cacheKey, $result, static::getCacheDuration());
//        return $result;
//    }


}
