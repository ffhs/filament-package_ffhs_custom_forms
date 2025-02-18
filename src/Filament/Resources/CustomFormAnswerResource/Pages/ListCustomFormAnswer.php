<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
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
