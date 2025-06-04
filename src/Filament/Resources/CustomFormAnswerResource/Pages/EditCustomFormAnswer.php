<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswerer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveFormAnswer;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCustomFormAnswer extends EditRecord
{
    use CanLoadFormAnswerer;
    use CanSaveFormAnswer;

    protected static string $resource = CustomFormAnswerResource::class;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                EmbeddedCustomForm::make('form_answerer')
                    ->autoViewMode()
                    ->columnSpanFull(),
            ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        if (!empty($customFormAnswer->customFieldAnswers)) {
            return $data;
        }

        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $this->form->getRecord();

        //Load datas from fields
        return ['form_answerer' => array_merge($data, $this->loadCustomAnswerData($customFormAnswer))];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $this->form->getRecord();
        $this->saveFormAnswer($customFormAnswer, $this->form, $data['form_answerer'], 'form_answerer');

        return [];
    }
}
