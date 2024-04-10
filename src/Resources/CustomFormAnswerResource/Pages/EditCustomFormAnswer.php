<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswerInput;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
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
        return array_merge($data, CustomFormRender::loadHelper($customFormAnswer));
    }

    protected function mutateFormDataBeforeSave(array $data): array {
        /**@var CustomFormAnswer  $customFormAnswer*/
        $customFormAnswer = $this->form->getRecord();
        CustomFormRender::saveHelper($customFormAnswer, $data);

        return [];
    }


    public function form(Form $form): Form {
        return $form
            ->schema([
                CustomFormAnswerInput::make()
                    ->autoViewMode()
                    ->columnSpanFull(),
            ]);
    }


}
