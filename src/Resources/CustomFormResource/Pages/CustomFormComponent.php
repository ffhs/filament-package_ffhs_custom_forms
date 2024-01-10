<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Contracts\HasForms;

class CustomFormComponent extends Repeater
{
    public static function make(string $name): static {
        $repeater = parent::make($name);

        $repeater
            ->maxItems(1)
            ->minItems(1)
            ->default([uniqid()=>[]])
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->label("")
            ->relationship()
            ->saveRelationshipsUsing(static function (Repeater $component, HasForms $livewire, ?array $state) {
                //ToDo
                dd($state);
            })

        ;

        return $repeater;
    }


}
