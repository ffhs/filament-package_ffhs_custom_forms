<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FlattedNested;

use ArrayAccess;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Support\Collection;
use Iterator;

class NestedFlattenList implements ArrayAccess
{

    protected Collection $data;

    protected ?string $fixedType = null;


    public static function make(array|Collection $items = [], ?string $type = null):static{
        return new static($items, $type);
    }


    public function __construct(array|Collection $items = [], ?string $type = null)
    {
        if($items instanceof Collection) $this->data = $items;
        else $this->data = collect($items);

        if(!is_null($type)) $this->fixedType = $type;
    }

    public function getStructure(array $keyMapping = []): array
    {
        return $this->loadStructure($this->data, $keyMapping);
    }

    public function addOnPosition(int $pos, NestingObject|array $data, ?string $key): void
    {
        $first = $this->data->first();

        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $data[$poseAttribute] = $pos;

        foreach ($this as $item){
            if($item[$poseAttribute] >= $pos) $item[$poseAttribute]+=1;
            if($item[$endPosAttribute] >= $pos) $item[$endPosAttribute]+=1;
        }

        if(is_null($key)) $this->data->add($data);
        else $this->data->put($key, $data);
    }

    public function removeFromPosition(int $pos, array $keyMapping = []): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $toRemove = $this->data->where($poseAttribute, $pos);

        foreach ($toRemove as $key => $item){
            $this->data->forget($key);
        }

        foreach ($this as $item){
            if($item[$poseAttribute] >= $pos) $item[$poseAttribute]-=1;
            if($item[$endPosAttribute] >= $pos) $item[$endPosAttribute]-=1;
        }
    }

    protected function loadStructure(Collection $objects, array $keyMapping): array {

        if($objects->count() === 0) return [];

        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $fields = $objects->sortBy($poseAttribute);
        $start = $objects->first()[$poseAttribute];
        $end = $objects->last()[$poseAttribute];

        $structure = [];

        for ($i = $start; $i <= $end; $i++) {
            /**@var array $field */
            $field = $fields->firstWhere($poseAttribute, $i);

            if($field == null) continue; //ToDo make a warning that the array is Damaged

            $key = array_search($field, $keyMapping);
            if(!$key) $key = $field['identifier'];

            if(empty($field[$endPosAttribute])) {
                $structure[$key] = [];
                continue;
            }

            $subFields = $fields
                ->where($poseAttribute, ">", $field[$poseAttribute])
                ->where($poseAttribute, "<=", $field[$endPosAttribute]);

            $i = $field[$endPosAttribute] ;

            $structure[$key] = static::loadStructure($subFields, $keyMapping);
        }

        return  $structure;
    }


    public function getPositionAttribute(): string
    {
        if(is_null($this->data->first())) return "";
        return $this->getType()::getPositionAttribute();

    }
    public function getEndContainerPositionAttribute(): string
    {
        if(is_null($this->data->first())) return "";
        return $this->getType()::getEndContainerPositionAttribute();
    }

    public function getType(): string
    {
        if(!is_null($this->fixedType)) return $this->fixedType;

        $first = $this->data->first();

        if(is_null($first)) return NestingObject::class;

        if(!($first instanceof NestingObject))
            throw new Exception("the objects must been instance of NestingObject");

        return $first::class;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->data->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
         $this->data->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->data->offsetUnset($offset);
    }


    public function getData():array
    {
       return $this->data->toArray();
    }
}
