<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\TemplateResource;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Throwable;

class EditTemplate extends EditCustomForm
{
    protected static string $resource = TemplateResource::class;



    protected function getSaveFormAction(): Action {
        return parent::getSaveFormAction()
            ->action(fn()=> $this->save())
            ->submit(null)
            ->requiresConfirmation(function ($livewire){
                return $this->showSaveConfirmation($livewire) || $this->showCollideMessage($livewire);
            })
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitAction(function ($action,$livewire){
                if($this->showCollideMessage($livewire)) $action->hidden();
                return $action;
            })
            ->modalSubmitActionLabel(function ($livewire): ?string {
                return $this->showSaveConfirmation($livewire)?
                    __('filament-panels::resources/pages/edit-record.form.actions.save.label'):
                    null;
            })
            ->modalDescription(function ($livewire){
                if($this->showCollideMessage($livewire)) return $this->collideMessageDescription($livewire);
                if($this->showSaveConfirmation($livewire)) return $this->saveConfirmationDescription($livewire);
                return null;
            })
            ->modalHeading(function ($livewire){
                if($this->showCollideMessage($livewire)) return $this->collideMessageHeading($livewire);
                if($this->showSaveConfirmation($livewire)) return $this->saveConfirmationHeading($livewire);
                return null;
            });
    }

    /**
     * @throws Throwable
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void {

        parent::save($shouldRedirect, $shouldSendSavedNotification);

        /*
         * Delete GeneralFields in Forms
         * Where one of the Template used GeneralField is used and move the answerers on this field.
         */

        /**@var CustomForm $template*/
        $template = CustomForm::query()->where("id",$this->record->id)->first();

        $templateGeneralFieldQuery = $template->customFields()->whereNotNull("general_field_id");
        $toDeleteGenFieldQuery = CustomField::query()
            ->whereIn("general_field_id", $templateGeneralFieldQuery->clone()->select("general_field_id"))
            ->whereIn("custom_form_id",
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

    protected function showSaveConfirmation($livewire):bool {
        return $this->cachedGeneralFieldsOverwritten($livewire)->count() > 0;
    }


    //If one Form existing witch have a template and this template has the same GeneralFields

    protected function cachedGeneralFieldsOverwritten($livewire): Collection {
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];
        $key = hash('sha256', json_encode($customFields));
        return Cache::remember("template_overwritten_gen_fields-".$key, 5,function() use ($livewire) {
            return $this->getGeneralFieldsOverwritten($livewire)->get();
        });
    }

    protected function getGeneralFieldsOverwritten($livewire): Builder { //ToDo Optimize Cache
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];
        $usedGeneralIDs = EditCustomFormHelper::getUsedGeneralFieldIds($customFields);

        $templateFieldsQuery = CustomField::query()
            ->where("template_id", $this->record->id);

        return CustomField::query()
            ->whereIn("custom_form_id",$templateFieldsQuery->select("custom_form_id"))
            ->whereIn("general_field_id",$usedGeneralIDs);
    }

    protected function showCollideMessage($livewire):bool {
        return $this->cachedTemplateGeneralFieldCollision($livewire)->count() > 0;
    }

    //If one Form existing witch have a template and this template has the same GeneralFields

    protected function cachedTemplateGeneralFieldCollision($livewire):Collection {
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];
        $key = hash('sha256', json_encode($customFields));
        return Cache::remember("template_collide_gen_fields-".$key, 5,function() use ($livewire) {
            return $this->getTemplateGeneralFieldCollisionQuery($livewire)->get();
        });
    }

    protected function getTemplateGeneralFieldCollisionQuery($livewire): Builder {
        $customFields = array_values($livewire->getCachedForms())[0]->getRawState()["custom_fields"];

        $templateFieldsQuery = CustomField::query()
            ->where("template_id", $this->record->id);

        $otherTemplatesQuery= CustomField::query()
            ->whereIn("custom_form_id",$templateFieldsQuery->select("custom_form_id"))
            ->whereNotNull("template_id")
            ->whereNot("template_id",$this->record->id);

        return CustomField::query()
            ->whereIn("custom_form_id", $otherTemplatesQuery->select("template_id"))
            ->whereIn("general_field_id",EditCustomFormHelper::getUsedGeneralFieldIds($customFields));
    }

    protected function collideMessageDescription($livewire): HtmlString {
        $generalFields = GeneralField::query()
            ->where("id",$this->getTemplateGeneralFieldCollisionQuery($livewire)->select("general_field_id"))
            ->get()->pluck("name_de"); //ToDo Translate

        $templates = CustomForm::query()
            ->where("id", $this->getTemplateGeneralFieldCollisionQuery($livewire)->select("custom_form_id"))
            ->get();

        $collidedForms = $this->getCollidedFormQuery($livewire)->get();

        $styleClasses = 'class="fi-modal-description text-sm text-gray-500 dark:text-gray-400 mt-2"';
        return
            new HtmlString("Es existieren Formulare, mit mehren Importierten Templates,
            diese Templates haben überschneidende generelle Felder. Entfernen sie die überschneidende
            generelle Felder oder lösen sie das Template in den anderen Formularen auf.
            <ul>
                <li><p ".$styleClasses.">Folgende generelle Felder sind betroffen: " . $generalFields . "</p></li>
                <li><p ".$styleClasses.">Folgende Templates sind betroffen: " . $templates->pluck("short_title") . "</p></li>
                <li><p ".$styleClasses.">Folgende Formulare sind betroffen: " . $collidedForms->pluck("short_title"). "</p></li>
            </ul>"); //ToDo Translate
    }
//

    protected function getCollidedFormQuery($livewire): Builder {
        $collidedFields = $this->cachedTemplateGeneralFieldCollision($livewire);
        $collidedTemplates = [];
        $collidedFields->each(function (CustomField $field) use (&$collidedTemplates){
            $collidedTemplates[$field->custom_form_id] = $field->custom_form_id;
        });

        $collideTemplatesUsedFields = CustomField::query()->whereIn("template_id", $collidedTemplates);
        $collidedFormsIds = CustomField::query()
            ->whereIn("custom_form_id",$collideTemplatesUsedFields->select("custom_form_id"))
            ->where("template_id", $this->record->id)
            ->select("custom_form_id");

        return CustomForm::query()->whereIn("id", $collidedFormsIds);
    }

    protected function saveConfirmationDescription($livewire): HtmlString {

        $styleClasses = "class='fi-modal-description text-sm text-gray-500 dark:text-gray-400 mt-2'";
        $generalFields= GeneralField::query()
            ->whereIn("id",$this->cachedGeneralFieldsOverwritten($livewire)->select("general_field_id"))
            ->select("name_de")
            ->get()
            ->pluck("name_de");  //ToDo Translate

        $forms= CustomForm::query()
            ->whereIn("id",$this->cachedGeneralFieldsOverwritten($livewire)->select("custom_form_id"))
            ->select("short_title")
            ->get()
            ->pluck("short_title");

        return
            new HtmlString(
                "Es existieren gleiche generelle Felder in anderen Formularen,
                welche dieses Template importiert haben. Diese Felder werden Gelöscht und die
                existierenden Antworten übernommen.
                <ul>
                    <li><p ".$styleClasses.">Folgende generelle Felder sind betroffen: " . $generalFields . "</p></li>
                    <li><p ".$styleClasses.">Folgende Formulare sind betroffen: " . $forms. "</p></li>
                </ul>"
            ); //ToDo Translate
    }

    protected function collideMessageHeading($livewire): string {
        $collidedForms = $this->getCollidedFormQuery($livewire)->get();
        return "Es gibt Kollisionen mit den generellen Felder und anderen Templates ("
            .$collidedForms->count().") "; //ToDo Translate
    }

    protected function saveConfirmationHeading($livewire): string {
        $count = $this->cachedGeneralFieldsOverwritten($livewire)->count();
        if($count == 1) return "Achtung es wird ein Feld in dem anderen Formular gelöscht!"; //ToDo Translate
        return "Achtung es werden ". $count . " Felder in den anderen Formularen gelöscht!"; //ToDo Translate
    }


}
