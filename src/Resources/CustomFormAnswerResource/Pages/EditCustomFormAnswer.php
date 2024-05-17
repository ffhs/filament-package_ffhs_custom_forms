<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\InfolistRender\CustomFormInfolist;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCustomFormAnswer extends EditRecord
{
    protected static string $resource = CustomFormAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array {
        $data= parent::mutateFormDataBeforeFill($data);

        if(!empty($customFormAnswer->customFieldAnswers)) return $data;
        /**@var CustomFormAnswer  $customFormAnswer*/
        $customFormAnswer = $this->form->getRecord();

        //Load datas from fields
        return array_merge($data, CustomFormLoadHelper::load($customFormAnswer));
    }

    protected function mutateFormDataBeforeSave(array $data): array {
        /**@var CustomFormAnswer  $customFormAnswer*/
        $customFormAnswer = $this->form->getRecord();
        CustomFormSaveHelper::save($customFormAnswer, $data);

        return [];
    }


    public function form(Form $form): Form {
        return $form
            ->schema([
                CustomFormInfolist::make()
                    ->autoViewMode()
                    ->columnSpanFull(),
            ]);
    }


}
