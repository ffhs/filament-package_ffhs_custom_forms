<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    public function form(Form $form): Form {
        return $form
            ->schema(
                [
                    Section::make()
                        ->schema(CustomFormEditForm::formSchema())
                        ->columns(3),
                ]
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
