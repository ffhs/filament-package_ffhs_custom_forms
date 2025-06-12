<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested;

use Exception;
use Illuminate\Support\Collection;

class NestedFlattenList
{

    protected Collection $data;

    protected ?string $fixedType = null;

    public function __construct(array|Collection $items = [], ?string $type = null)
    {
        if ($items instanceof Collection) {
            $this->data = $items;
        } else {
            $this->data = collect($items);
        }

        if (!is_null($type)) {
            $this->fixedType = $type;
        }
    }

    public static function make(array|Collection $items = [], ?string $type = null): static
    {
        return new static($items, $type);
    }

    public function getStructure(bool $useKey = false): array
    {
        return $this->loadStructure($this->data, $useKey);
    }

    public function getPositionAttribute(): string
    {
        if (!$this->getType()) {
            return '';
        }
        return $this->getType()::getPositionAttribute();

    }

    public function getType(): string
    {
        if (!is_null($this->fixedType)) {
            return $this->fixedType;
        }

        $first = $this->data->first();

        if (is_null($first)) {
            return NestedListElement::class;
        }

        if (!($first instanceof NestingObject)) {
            throw new Exception('the objects must been instance of NestingObject');
        }

        return $first::class;
    }

    public function getEndContainerPositionAttribute(): string
    {
        if (!$this->getType()) {
            return '';
        }
        return $this->getType()::getEndContainerPositionAttribute();
    }

    public function addOnPosition(int $pos, NestingObject|array $data, ?string $key): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $data[$poseAttribute] = $pos;

        foreach ($this->data as $elementKey => $element) {
            if ($element[$poseAttribute] >= $pos) {
                $element[$poseAttribute] += 1;
            }
            if (!empty($element[$endPosAttribute]) && $element[$endPosAttribute] >= $pos) {
                $element[$endPosAttribute] += 1;
            }
            $this->data->put($elementKey, $element);
        }

        if (is_null($key)) {
            $this->data->add($data);
        } else {
            $this->data->put($key, $data);
        }
    }

    public function addManyOnPosition(int $startPos, array $elementsToAdd, bool $withKeys = false): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $amountToAdd = count($elementsToAdd);

        //Rearrange
        foreach ($this->data as $itemKey => $item) {
            if ($item[$poseAttribute] >= $startPos) {
                $item[$poseAttribute] += $amountToAdd;
            }
            if ($item[$endPosAttribute] >= $startPos) {
                $item[$endPosAttribute] += $amountToAdd;
            }
            $this->data->put($itemKey, $item);
        }

        foreach ($elementsToAdd as $key => $newElement) {
            $newElement[$poseAttribute] += $startPos - 1;
            // If it has an end position
            if (array_key_exists($endPosAttribute, $newElement) && !is_null($newElement[$endPosAttribute])) {
                $newElement[$endPosAttribute] += $startPos - 1;
            }

            if ($withKeys) {
                $this->data->put($key, $newElement);
            } else {
                $this->data->add($newElement);
            }
        }

        //dd( collect($this->data)->sortBy($poseAttribute)->toArray());
    }

    public function removeFromPosition(int $pos): void
    {
        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $toRemove = $this->data->where($poseAttribute, $pos)->first();


        //Delete Sub Elements

        $endPos = $toRemove[$endPosAttribute] ?? $pos;
        $keysToDelete = [];

        foreach ($this->data as $key => $element) {
            if ($pos <= $element[$poseAttribute] && $endPos >= $element[$poseAttribute]) {
                $keysToDelete[] = $key;
            }
        }

        foreach ($keysToDelete as $key) {
            $this->data->forget($key);
        }

        $amountDeletedFields = count($keysToDelete);

        //Rearrange Fields
        foreach ($this->data as $key => $item) {
            if ($item[$poseAttribute] >= $pos) {
                $item[$poseAttribute] -= $amountDeletedFields;
            }
            if (!empty($item[$endPosAttribute]) && $item[$endPosAttribute] >= $pos) {
                $item[$endPosAttribute] -= $amountDeletedFields;
            }
            $this->data->put($key, $item);
        }

    }

    public function getData(): array
    {
        return $this->data->toArray();
    }

    protected function loadStructure(Collection $objects, bool $useKey): array
    {

        if ($objects->count() === 0) {
            return [];
        }

        $poseAttribute = $this->getPositionAttribute();
        $endPosAttribute = $this->getEndContainerPositionAttribute();

        $objects = $objects->sortBy($poseAttribute);
        $start = $objects->first()[$poseAttribute];
        $end = $objects->last()[$poseAttribute];

        $structure = [];

        for ($i = $start; $i <= $end; $i++) {

            $result = $objects->where($poseAttribute, $i);
            $field = $result->first();
            $originalKey = $result->keys()->first();

            if ($field == null) {
                continue;
            } //ToDo make a warning that the array is Damaged

            //GetKey
            $key = null;
            if ($useKey) {
                $key = $originalKey;
            }
            if (is_null($key)) {
                $key = $field['identifier'];
            }

            if (empty($field[$endPosAttribute])) {
                $structure[$key] = [];
                continue;
            }

            $subFields = $objects
                ->where($poseAttribute, '>', $field[$poseAttribute])
                ->where($poseAttribute, '<=', $field[$endPosAttribute]);

            $i = $field[$endPosAttribute];

            $structure[$key] = static::loadStructure($subFields, $useKey);
        }

        return $structure;
    }
}
