<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class CreateCustomForm extends CreateRecord
{
    protected static string $resource = CustomFormResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('short_title')
                ->label(CustomForm::__('attributes.short_title'))
                ->required(),
            CustomFormTypeSelector::make()
                ->required()
        ]);
    }
}
