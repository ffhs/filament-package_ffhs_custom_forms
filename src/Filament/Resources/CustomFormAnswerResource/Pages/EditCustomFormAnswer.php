<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\CustomFormComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        if (!empty($customFormAnswer->customFieldAnswers)) {
            return $data;
        }
        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $this->form->getRecord();

        //Load datas from fields
        return array_merge($data, CustomFormLoadHelper::load($customFormAnswer));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $this->form->getRecord();
        CustomForms::save($customFormAnswer, $this->form);

        return [];
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CustomFormComponent::make()
                    ->autoViewMode()
                    ->columnSpanFull(),
            ]);
    }


}
