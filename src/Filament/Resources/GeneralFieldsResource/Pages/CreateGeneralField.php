<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateGeneralField extends CreateRecord
{
    protected static string $resource = GeneralFieldResource::class;
    use CreateRecord\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}
