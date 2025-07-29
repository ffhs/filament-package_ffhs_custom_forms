<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class CreateGeneralField extends CreateRecord
{
    //use Translatable;;
    use HasGeneralFieldForm;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return GeneralField::__('pages.create.title');
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
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
