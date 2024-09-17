<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RelationCachedInformations
{
    private string $modelClass;
    private array $ids;
    private bool $collection;

    public function __construct(string $modelClass, array $ids, bool $collection = true)
    {
        $this->modelClass = $modelClass;
        $this->ids = $ids;
        $this->collection = $collection;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function isCollection(): bool
    {
        return $this->collection;
    }


    public function getModels(): null|Model|Collection
    {
        $modelClass = $this->getModelClass();
        $emptyModel = new $modelClass();
        /**@var Model|CachedModel $emptyModel **/

        $ids = $this->getIds();
        $cached = $emptyModel::getModelCache()->whereIn("id", $ids);

        $cached = Collection::make($cached);
        if($cached->count() == sizeof($ids)) return $this->isCollection() ? $cached: $cached->first();

        //Get the missing models
        $missingIds = collect($ids)->whereNotIn("id", $cached->pluck("id"));
        $result = $emptyModel::query()->whereIn("id", $missingIds)->get();

        $emptyModel::addToModelCache($result);

        $cached = $result->merge($cached);

        return $this->isCollection() ? $cached: $cached->first();
    }


}
