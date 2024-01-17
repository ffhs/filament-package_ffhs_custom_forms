<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRenderForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnsweResource;
use Filament\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCustomFormAnswer extends EditRecord
{
    protected static string $resource = CustomFormAnsweResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array {
        $data= parent::mutateFormDataBeforeFill($data);

        /**@var CustomFormAnswer  $customFormAnsware*/
        $customFormAnsware = CustomFormAnswer::query()->firstWhere("id", $data["id"]);
        if($customFormAnsware->customForm->getFormConfiguration()::hasVariations()){
            if(!empty($customFormAnsware->customFieldAnswers)){
                $variation= $customFormAnsware->customFieldAnswers
                    ->map(fn(CustomFieldAnswer $answer)=>$answer->customFieldVariation)
                    ->flatten(1)
                    ->firstWhere("custom_field_variation_id","!=",null);
                if(!is_null($variation))  $data["variation"]= $variation->id;
            }
        }

        return $data;
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
                            $isCreating = empty($record->customFieldAnswers);
                            $viewMode = $isCreating? $formConfiguration::displayCreateMode() : $formConfiguration::displayEditMode();


                            $hasVariations = $record->customForm->getFormConfiguration()::hasVariations();
                            $variation = null;
                            if($hasVariations) $record->customForm->variationModels()->firstWhere("id",$get("variation"));

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
