<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;


abstract class CachedModel extends Model
{

    //"relation" => ["local_key, "key"]
    protected array $cachedRelations = [

    ];

    protected static array $cacheWith = [

    ];

    public function __get($key) {
        if(empty($this->cachedRelations[$key])) return parent::__get($key);
        $relationData = $this->cachedRelations[$key];
        $localKey = $relationData[0];
        $relatedKey = $relationData[1];

        /**@var Relation $relation*/
        $relation = $this->$key();
        return $relation->getRelated()::cached($this->$localKey,$relatedKey);
    }


    public static function allCached(mixed $custom_field_id): ?Collection{
        return Cache::remember(
            (new static())->table."-all" .$custom_field_id, config('ffhs_custom_forms.cache_duration'),
            fn()=>static::all()
        );
    }

    public static function singleListCached(): ?Collection{
        return Cache::get(static::getFromSingedListName());
    }


    protected static function getFromSingedListName(): string {
         return (new static())->getTable(). "cached_list";
    }

    public static function addToCachedList(Collection|CachedModel $toAdd): void {
        if(static::class === FieldRule::class) dd($toAdd);
        $cachedList = Cache::get(static::getFromSingedListName());
        if(is_null($cachedList)) $cachedList = collect();
        if($toAdd instanceof Collection) $cachedList =
            $cachedList->merge($toAdd->whereNotIn("id",$cachedList->pluck("id")));
        else if(!in_array($toAdd->id, $cachedList->pluck("id")->toArray())) $cachedList = $cachedList->add($toAdd);

        Cache::set(static::getFromSingedListName(), $cachedList, config('ffhs_custom_forms.cache_duration'));
    }


    public static function cached(mixed $value, string $attribute = "id", bool $searching = true): ?static{
        $output = Cache::get(static::getFromSingedListName())?->where($attribute,$value)->first();
        if(!is_null($output)) return $output;
        if(!$searching) return $output;
        $output = static::query()->where($attribute, $value)->with(static::$cacheWith)->first();
        if(is_null($output)) return null;
        static::addToCachedList($output);
        return $output;
    }


}
