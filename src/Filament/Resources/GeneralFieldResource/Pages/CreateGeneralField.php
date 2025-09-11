<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateGeneralField extends CreateRecord
{
    //use Translatable;;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return GeneralField::__('pages.create.title');
    }

    protected function getHeaderActions(): array
    {
        return [
//            LocaleSwitcher::make(),
        ];
    }
}
