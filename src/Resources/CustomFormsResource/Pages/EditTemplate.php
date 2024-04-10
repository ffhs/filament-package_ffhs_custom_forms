<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFormFieldFunctions;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Throwable;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;



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


    protected function getGeneralFieldsOverwritten($livewire,CustomForm $record): Builder { //ToDo Optimize Cache
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];
        $usedGeneralIDs = EditCustomFormFieldFunctions::getUsedGeneralFieldIds($customFields);

        $templateFieldsQuery = CustomField::query()
            ->where("template_id", $record->id);

        return CustomField::query()
            ->whereIn("custom_form_id",$templateFieldsQuery->select("custom_form_id"))
            ->whereIn("general_field_id",$usedGeneralIDs);
    }

    public function getGeneralFieldsOverwrittenCached($livewire, $record): Collection {
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];
        $key = hash('sha256', json_encode($customFields));
        return Cache::remember("template_overwritten_gen_fields-".$key, 5,function() use ($record, $livewire) {
            return $this->getGeneralFieldsOverwritten($livewire,$record)->get();
        });
    }

    public function showSaveConfirmation($livewire, $record):bool {
        return $this->getGeneralFieldsOverwrittenCached($livewire, $record)->count() > 0;
    }

    protected function getSaveFormAction(): Action { //ToDo fixing
        return parent::getSaveFormAction()
            ->modalSubmitActionLabel("Bestätigen") //ToDo Translate
            ->action(fn()=> $this->save())
            ->submit(null)
            ->requiresConfirmation(fn ($livewire, $record) => $this->showSaveConfirmation($livewire, $record))
            ->modalDescription(function ($livewire,$record){
                if(!$this->showSaveConfirmation($livewire, $record)) return null;

                return "Es existieren gleiche generelle Felder in anderen Formularen,
                welche dieses Template importiert haben. Diese Felder werden Gelöscht und die
                existierenden Antworten übernommen"; //ToDo Translate
            })
            ->modalSubmitActionLabel(function ($livewire,$record): ?string {
                return $this->showSaveConfirmation($livewire, $record)?
                    __('filament-panels::resources/pages/edit-record.form.actions.save.label'):
                    null;
            })
            ->modalHeading(function ($livewire, $record){
                $count = $this->getGeneralFieldsOverwrittenCached($livewire,$record)->count();
                return $this->showSaveConfirmation($livewire, $record)?
                    "Achtung es werden ". $count . " Feld/er in den anderen Formularen gelöscht!": //ToDo Translate
                    null;
            });
    }


    /**
     * @throws Throwable
     */
    public function save(bool $shouldRedirect = true): void {
        parent::save($shouldRedirect);

        /*
         * Delete GeneralFields in Forms
         * Where one of the Template used GeneralField is used and move the answerers on this field.
         */

        /**@var CustomForm $template*/
        $template = CustomForm::query()->where("id",$this->record->id)->first();

        $templateGeneralFieldQuery = $template->customFields()->whereNotNull("general_field_id");
        $toDeleteGenFieldQuery = CustomField::query()
            ->with(["answers"])
            ->whereIn("general_field_id", $templateGeneralFieldQuery->clone()->select("general_field_id"))
            ->where("custom_form_id",
                CustomField::query()->where("template_id",$template->id)->select("custom_form_id")
            );

        $toDeleteGenFields = $toDeleteGenFieldQuery->clone()->get();

        if($toDeleteGenFields->count() == 0) return;

        $newGeneralFields = $templateGeneralFieldQuery->select(["general_field_id","id"])->get();

        foreach ($toDeleteGenFields as $generalField){
            /**@var CustomField $generalField*/
            $newGeneralField = $newGeneralFields
                ->where("general_field_id", $generalField->general_field_id)
                ->first();
            $generalField->answers()->update(["custom_field_id" => $newGeneralField->id]);
        }

        $toDeleteGenFieldQuery->delete();

        //Reorder
        $toReorderForm = CustomForm::query()->whereIn("id",$toDeleteGenFieldQuery->select("custom_form_id"));

        foreach ($toReorderForm as $form){ //toDo Optimize
            /**@var CustomForm $form */
            $fields = $form->customFields;

            $position = 0;
            foreach ($fields as $field){
                /**@var CustomField $field */
                $position++;
                $field->layout_end_position = $position;
                if($field->isDirty()) $field->save();
            }

        }

    }


}
