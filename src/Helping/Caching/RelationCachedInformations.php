<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\Caching;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RelationCachedInformations
{
    private string $modelClass;
    private array $ids;

    public function __construct(string $modelClass, array $ids)
    {
        $this->modelClass = $modelClass;
        $this->ids = $ids;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getModels(): Collection
    {
        $modelClass = $this->getModelClass();
        $emptyModel = new $modelClass();
        /**@var Model|CachedModel $emptyModel **/

        $ids = $this->getIds();
        $cached = $emptyModel::getModelCache()->whereIn("id", $ids);

        if($cached->count() == sizeof($ids)) return $cached;

        $missingIds = collect($ids)->whereNotIn("id", $cached->pluck("id"));
        $result = $emptyModel::query()->whereIn("id", $missingIds)->get();

        $emptyModel::addToModelCache($result);

        return $result->merge($cached);
    }


}
