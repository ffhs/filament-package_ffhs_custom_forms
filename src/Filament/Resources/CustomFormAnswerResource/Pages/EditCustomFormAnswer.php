<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveFormAnswer;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomFormAnswer extends EditRecord
{
    use CanLoadFormAnswer;
    use CanSaveFormAnswer;

    protected static string $resource = CustomFormAnswerResource::class;

    public function getTitle(): string|Htmlable
    {
        $attributes = $this->getRecord()->attributesToArray();
        return trans(CustomFormAnswer::__('pages.edit.title'), $attributes);
    }


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
            ViewAction::make()
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

        //Load data's from fields
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
