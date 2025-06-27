<?php

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms')
    //->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump']);

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Models')
    ->toBeClasses()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Traits')
    ->toBeTraits();
