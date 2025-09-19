<?php

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms')
    //->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump', 'Debugbar']);

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Models')
    ->toBeClasses()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Traits')
    ->toBeTraits();

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Enums')
    ->toBeEnums();

arch()
    ->expect('Ffhs\FilamentPackageFfhsCustomForms\Contracts')
    ->toBeInterfaces();
