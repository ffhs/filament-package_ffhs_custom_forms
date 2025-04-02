<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateGeneralField extends CreateRecord
{
    use Translatable;
    use HasGeneralFieldForm;

    protected static string $resource = GeneralFieldResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                $this->getGeneralFieldBasicSettings(),
                $this->getOverwriteTypeOptions(),
                $this->getGeneralFieldTypeOptions(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
