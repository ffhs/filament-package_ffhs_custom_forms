<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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
        $data =array_merge($data, CustomFormRender::loadHelper($customFormAnswer));

        $customForm =  CustomForm::cached($customFormAnswer->custom_form_id);
        if($customForm->getFormConfiguration()::hasVariations()){
                $variation= $customFormAnswer->customFieldAnswers
                    ->map(fn(CustomFieldAnswer $answer)=>$answer->customFieldVariation)
                    ->filter(fn(CustomFieldVariation $variation)=>is_null($variation->variation_id))->first();
                if(!is_null($variation))  $data["variation"]= $variation->id;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array {
        /**@var CustomFormAnswer  $customFormAnswer*/
        $customFormAnswer = $this->form->getRecord();
        $variation= $customFormAnswer->customFieldAnswers
            ->map(fn(CustomFieldAnswer $answer)=>$answer->customFieldVariation)
            ->flatten(1)
            ->firstWhere("custom_field_variation_id","!=",null);
        if(is_null($variation))$variation = -1;
        CustomFormRender::saveHelper($customFormAnswer, $data, $variation);

        return [];
    }


    public function form(Form $form): Form {
        return $form
                ->schema([
                    Select::make("variation")
                        ->visible(fn(CustomFormAnswer $record)=> CustomForm::cached($record->custom_form_id)->getFormConfiguration()::hasVariations())
                        ->disabled(fn($get)=> !is_null($get("variation")))
                        ->live()
                        ->options(function(CustomFormAnswer $record){
                            $customForm =  CustomForm::cached($record->custom_form_id);
                            $formConfiguration = $customForm->getFormConfiguration();
                            $variationModels = $customForm->variationModels()->get();
                            $keys = $variationModels->map(fn(Model $model) => $model->id)->toArray();
                            $value = $variationModels->map(fn(Model $model) => $formConfiguration::variationName($model))->toArray();
                            return array_combine($keys,$value);
                        }),

                    Group::make()
                        ->schema(function(CustomFormAnswer $record,$get){
                            $customForm =  CustomForm::cached($record->custom_form_id);
                            $formConfiguration = $customForm->getFormConfiguration();
                            $isCreating = $record->customFieldAnswers->count() ==0;
                            $viewMode = $isCreating? $formConfiguration::displayCreateMode() : $formConfiguration::displayEditMode();

                            $hasVariations = $customForm->getFormConfiguration()::hasVariations();
                            $variation = null;
                            if($hasVariations) $variation =$customForm->variationModels()->firstWhere("id",$get("variation"));

                            return CustomFormRender::generateFormSchema($customForm, $viewMode,$variation);
                        })
                        ->visible(function(CustomFormAnswer $record,$get){
                            $customForm =  CustomForm::cached($record->custom_form_id);
                            $hasVariations = $customForm->getFormConfiguration()::hasVariations();
                            return  !$hasVariations || !is_null($get("variation"));
                        })
                        ->columnSpanFull(),
                ]);
    }


}
