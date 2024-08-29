<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource;
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
