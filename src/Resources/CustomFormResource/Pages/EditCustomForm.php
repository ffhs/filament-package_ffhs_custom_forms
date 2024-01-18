<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;

class EditCustomForm extends EditRecord
{
    protected static string $resource = CustomFormResource::class;

    public function form(Form $form): Form {
        return $form
            ->schema(
                array_merge(
                    CustomFormEditForm::formSchema(),
                    [
                           \Filament\Forms\Components\Actions::make([ //ToDo remove
                                Action::make("test1")
                                    ->modalWidth('7xl')
                                    ->form(fn($record)=>CustomFormRender::generateFormSchema($record,"default"))
                                    ->action(fn()=> dd("??"))
                           ])
                    ]
                )
            );
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }





}
