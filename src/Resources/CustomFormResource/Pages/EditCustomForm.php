<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCustomForm extends EditRecord
{
    protected static string $resource = CustomFormResource::class;

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
