<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;
use Illuminate\Contracts\Support\Htmlable;

class CreateGeneralField extends CreateRecord
{
    use Translatable;
    use HasGeneralFieldForm;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return GeneralField::__('pages.create.title');
    }

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
