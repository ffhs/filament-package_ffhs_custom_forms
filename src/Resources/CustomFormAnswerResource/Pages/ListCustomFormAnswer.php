<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomFormAnswer extends ListRecords
{
    protected static string $resource = CustomFormAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
