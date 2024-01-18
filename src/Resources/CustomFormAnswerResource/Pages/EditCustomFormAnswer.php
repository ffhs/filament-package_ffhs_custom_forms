<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRenderForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
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
        $data =array_merge($data, CustomFormRenderForm::loadHelper($customFormAnswer));

        if($customFormAnswer->customForm->getFormConfiguration()::hasVariations()){
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
        CustomFormRenderForm::saveHelper($customFormAnswer, $data, $variation);

        return [];
    }


    public function form(Form $form): Form {
        return $form
                ->schema([
                    Select::make("variation")
                        ->visible(fn(CustomFormAnswer $record)=> $record->customForm->getFormConfiguration()::hasVariations())
                        ->disabled(fn($get)=> !is_null($get("variation")))
                        ->live()
                        ->options(function(CustomFormAnswer $record){
                            $formConfiguration = $record->customForm->getFormConfiguration();
                            $variationModels = $record->customForm->variationModels()->get();
                            $keys = $variationModels->map(fn(Model $model) => $model->id)->toArray();
                            $value = $variationModels->map(fn(Model $model) => $formConfiguration::variationName($model))->toArray();
                            return array_combine($keys,$value);
                        }),

                    Group::make()
                        ->schema(function(CustomFormAnswer $record,$get){
                            $formConfiguration = $record->customForm->getFormConfiguration();
                            $isCreating = $record->customFieldAnswers->count() ==0;
                            $viewMode = $isCreating? $formConfiguration::displayCreateMode() : $formConfiguration::displayEditMode();

                            $hasVariations = $record->customForm->getFormConfiguration()::hasVariations();
                            $variation = null;
                            if($hasVariations) $variation =$record->customForm->variationModels()->firstWhere("id",$get("variation"));

                            return CustomFormRenderForm::generateFormSchema($record->customForm, $viewMode,$variation);
                        })
                        ->visible(function(CustomFormAnswer $record,$get){
                            $hasVariations = $record->customForm->getFormConfiguration()::hasVariations();
                            return  !$hasVariations || !is_null($get("variation"));
                        })
                        ->columnSpanFull(),
                ])
            ;
    }


}
