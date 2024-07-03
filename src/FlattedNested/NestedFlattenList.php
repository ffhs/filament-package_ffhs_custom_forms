<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\FlattedNested;

use ArrayAccess;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Support\Collection;
use Iterator;

class NestedFlattenList
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
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $data[$poseAttribute] = $pos;

        foreach ($this->data as $itemKey => $item){
            if($item[$poseAttribute] >= $pos) $item[$poseAttribute]+=1;
            if($item[$endPosAttribute] >= $pos) $item[$endPosAttribute]+=1;
            $this->data->put($itemKey, $item);
        }

        if(is_null($key)) $this->data->add($data);
        else $this->data->put($key, $data);
    }

    public function addManyOnPosition(int $startPos, array $elementsToAdd, bool $withKeys = false): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $amountToAdd = count($elementsToAdd);

        //Rearrange
        $startPos = $startPos + $amountToAdd;
        foreach ($this->data as $itemKey => $item){
            if($item[$poseAttribute] >= $startPos) $item[$poseAttribute]+=$amountToAdd;
            if($item[$endPosAttribute] >= $startPos) $item[$endPosAttribute]+=$amountToAdd;
            $this->data->put($itemKey, $item);
        }


        $count = 0;
        foreach ($elementsToAdd as $key => $newElement){
            $newElement[$poseAttribute] += $startPos + $count;
            $count++;

            if($withKeys) $this->data->put($key, $newElement);
            else  $this->data->add($newElement);
        }

    }


    public function removeFromPosition(int $pos): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $toRemove = $this->data->where($poseAttribute, $pos)->first();


        //Delete Sub Elements

        $endPos = $toRemove[$endPosAttribute] ?? $pos;
        $keysToDelete = [];

        foreach ($this->data as $key => $element){
            if( $pos <= $element[$poseAttribute] && $endPos >= $element[$poseAttribute])
                $keysToDelete[] = $key;
        }

        foreach ($keysToDelete as $key){
            $this->data->forget($key);
        }

        $amountDeletedFields = count($keysToDelete);

        //Rearrange Fields
        foreach ($this->data as $key => $item){
            if($item[$poseAttribute] >= $pos) $item[$poseAttribute]-=$amountDeletedFields;
            if(!empty($item[$endPosAttribute]) && $item[$endPosAttribute] >= $pos)
                $item[$endPosAttribute] -= $amountDeletedFields;
            $this->data->put($key, $item);
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


    public function getData():array
    {
       return $this->data->toArray();
    }
}
