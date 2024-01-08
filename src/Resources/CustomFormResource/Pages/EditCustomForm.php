<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomForm extends EditRecord
{
    use FormForm;
    protected static string $resource = CustomFormResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
