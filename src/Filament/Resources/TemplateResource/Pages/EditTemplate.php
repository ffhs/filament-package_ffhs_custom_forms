<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\TemplateResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanGetUsedGeneralFields;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class EditTemplate extends EditCustomForm
{
    use CanGetUsedGeneralFields;

    protected static string $resource = TemplateResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return static::$resource::canAccess()
            && static::$resource::can('manageTemplates'); //ToDo improve
    }

    public function getTitle(): string|Htmlable
    {
        $attributes = $this
            ->getRecord()
            ->attributesToArray();

        return trans(CustomForm::__('pages.edit_template.title'), $attributes);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void //ToDo improve
    {
        if (!$this->showSaveConfirmation()) {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        }

        DB::transaction(function () use ($shouldSendSavedNotification, $shouldRedirect) {
            parent::save($shouldRedirect, $shouldSendSavedNotification);

            $template = $this->getRecord();

            $templateGeneralFieldQuery = $template
                ->customFieldsQuery()
                ->whereNotNull('general_field_id');

            $templateGeneralFieldQueryId = $templateGeneralFieldQuery
                ->clone()
                ->select('general_field_id');

            $customFormsWitchUseTemplate = CustomField::query()->where('template_id',
                $template->id)->select('custom_form_id');

            $toDeleteGenFieldQuery = CustomField::query()
                ->whereIn('general_field_id', $templateGeneralFieldQueryId)
                ->whereIn('custom_form_id', $customFormsWitchUseTemplate);

            $toDeleteGenFields = $toDeleteGenFieldQuery->get();

            if ($toDeleteGenFields->count() === 0) {
                return;
            }

            $newGeneralFields = $templateGeneralFieldQuery->select(['general_field_id', 'id'])->get();

            foreach ($toDeleteGenFields as $generalField) {
                /**@var CustomField $generalField */
                $newGeneralField = $newGeneralFields
                    ->where('general_field_id', $generalField->general_field_id)
                    ->first();

                /**@phpstan-ignore-next-line */
                $newGeneralFieldId = $newGeneralField->id;
                $generalField->answers()->update(['custom_field_id' => $newGeneralFieldId]);
            }

            $toDeleteGenFieldQuery->delete();

            //Reorder
            $toReorderForm = CustomForm::query()
                ->whereIn('id', $toDeleteGenFieldQuery->select('custom_form_id'))
                ->get();

            foreach ($toReorderForm as $form) {
                /**@var CustomForm $form */
                $fields = $form->customFields;
                $position = 0;

                foreach ($fields as $field) {
                    /**@var CustomField $field */
                    $position++;
                    $field->layout_end_position = $position;

                    if ($field->isDirty()) {
                        $field->save();
                    }
                }
            }
        });
    }

    public function getRecord(): CustomForm
    {
        return parent::getRecord();
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        return $this->getRecord()->getFormConfiguration();
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->action(fn() => $this->save())
            ->submit(null)
            ->requiresConfirmation(fn() => $this->showSaveConfirmation() || $this->showCollideMessage())
            ->modalWidth(Width::ExtraLarge)
            ->modalSubmitAction(function (Action $action) {
                if ($this->showCollideMessage()) {
                    $action->hidden();
                }

                return $action;
            })
            ->modalSubmitActionLabel(function () {
                return $this->showSaveConfirmation() ?
                    __('filament-panels::resources/pages/edit-record.form.actions.save.label') :
                    null;
            })
            ->modalDescription(function () {
                if ($this->showCollideMessage()) {
                    return $this->collideMessageDescription();
                }

                if ($this->showSaveConfirmation()) {
                    return $this->saveConfirmationDescription();
                }

                return null;
            })
            ->modalHeading(function () {
                if ($this->showCollideMessage()) {
                    return $this->collideMessageHeading();
                }

                if ($this->showSaveConfirmation()) {
                    return $this->saveConfirmationHeading();
                }

                return null;
            });
    }

    protected function showSaveConfirmation(): bool
    {
        return $this->cachedGeneralFieldsOverwritten()->count() > 0;
    }

    protected function showCollideMessage(): bool
    {
        return $this->getTemplateGeneralFieldCollision()->count() > 0;
    }

    protected function cachedGeneralFieldsOverwritten(): Collection
    {
        return once(fn(): Collection => $this->getGeneralFieldsOverwrittenQuery()->get());
    }

    protected function getGeneralFieldsOverwrittenQuery(): Builder
    {
        $configuration = $this->getFormConfiguration();
        $customFields = $this->data['custom_form']['custom_fields'] ?? [];
        $usedGeneralIds = $this->getUsedGeneralFieldIds($customFields, $configuration);

        $templateFieldsQuery = CustomField::query()
            ->where('template_id', $this->getRecord()->id);

        return CustomField::query()
            ->whereIn('custom_form_id', $templateFieldsQuery->select('custom_form_id'))
            ->whereIn('general_field_id', $usedGeneralIds);
    }

    protected function getTemplateGeneralFieldCollision(): Collection
    {
        return once(fn(): Collection => $this->getTemplateGeneralFieldCollisionQuery()->get());
    }

    protected function getTemplateGeneralFieldCollisionQuery(): Builder
    {
        /**@phpstan-ignore-next-line */
        $id = $this->record->id;
        $customFields = $this->form->getState()['custom_form']['custom_fields'] ?? [];
        $usedGeneralFieldIds = $this->getUsedGeneralFieldIds($customFields, $this->getFormConfiguration());

        $templateFieldsQuery = CustomField::query()
            ->where('template_id', $id);

        $otherTemplatesQuery = CustomField::query()
            ->whereIn('custom_form_id', $templateFieldsQuery->select('custom_form_id'))
            ->whereNotNull('template_id')
            ->whereNot('template_id', $id);

        return CustomField::query()
            ->whereIn('custom_form_id', $otherTemplatesQuery->select('template_id'))
            ->whereIn('general_field_id', $usedGeneralFieldIds);
    }

    protected function collideMessageDescription(): HtmlString
    {
        $generalFields = GeneralField::query()
            ->where('id', $this->getTemplateGeneralFieldCollisionQuery()->select('general_field_id'))
            ->get()
            ->pluck('name_' . App::currentLocale());

        $templates = CustomForm::query()
            ->where('id', $this->getTemplateGeneralFieldCollisionQuery()->select('custom_form_id'))
            ->get();

        $collidedForms = $this->getCollidedFormQuery()->get();
        $styleClasses = 'class="fi-modal-description text-sm text-gray-500 dark:text-gray-400 mt-2"';

        return
            new HtmlString(
                'Es existieren Formulare, mit mehreren importierten Templates,
            diese Templates haben überschneidende generelle Felder. Entfernen Sie die überschneidende
            generelle Felder oder lösen sie das Template in den anderen Formularen auf.
            <ul>
                <li><p ' . $styleClasses . '>
                    Folgende generelle Felder sind betroffen: ' . $generalFields . '
                </p></li>
                <li><p ' . $styleClasses . '>
                    Folgende Templates sind betroffen: ' . $templates->pluck('short_title') . '
                </p></li>
                <li><p ' . $styleClasses . '>
                    Folgende Formulare sind betroffen: ' . $collidedForms->pluck('short_title') . '
                </p></li>
            </ul>'
            ); //ToDo Translate
    }

    protected function getCollidedFormQuery(): Builder
    {
        /**@phpstan-ignore-next-line */
        $id = $this->record->id;
        $collidedFields = $this->getTemplateGeneralFieldCollision();
        $collidedTemplates = [];
        $collidedFields->each(function (CustomField $field) use (&$collidedTemplates) {
            $collidedTemplates[$field->custom_form_id] = $field->custom_form_id;
        });

        $collideTemplatesUsedFields = CustomField::query()
            ->whereIn('template_id', $collidedTemplates);
        $collidedFormsIds = CustomField::query()
            ->whereIn('custom_form_id', $collideTemplatesUsedFields->select('custom_form_id'))
            ->where('template_id', $id)
            ->select('custom_form_id');

        return CustomForm::query()
            ->whereIn('id', $collidedFormsIds);
    }

    protected function saveConfirmationDescription(): HtmlString
    {
        $styleClasses = 'class="fi-modal-description text-sm text-gray-500 dark:text-gray-400 mt-2"';
        $generalFields = GeneralField::query()
            ->whereIn('id', $this->cachedGeneralFieldsOverwritten()->select('general_field_id'))
            ->pluck('name');

        $forms = CustomForm::query()
            ->whereIn('id', $this->cachedGeneralFieldsOverwritten()->select('custom_form_id'))
            ->pluck('short_title');

        return
            new HtmlString(
                'Es existieren gleiche generelle Felder in anderen Formularen,
                welche dieses Template importiert haben. Diese Felder werden Gelöscht und die
                existierenden Antworten übernommen.
                <ul>
                    <li><p ' . $styleClasses . '>
                        Folgende generelle Felder sind betroffen: ' . $generalFields . '
                    </p></li>
                    <li><p ' . $styleClasses . '>
                        Folgende Formulare sind betroffen: ' . $forms . '
                    </p></li>
                </ul>'
            ); //ToDo Translate
    }

    protected function collideMessageHeading(): string
    {
        $collidedForms = $this
            ->getCollidedFormQuery()
            ->get();

        return 'Es gibt Kollisionen mit den generellen Felder und anderen Templates ('
            . $collidedForms->count() . ') '; //ToDo Translate
    }

    protected function saveConfirmationHeading(): string
    {
        $count = $this
            ->cachedGeneralFieldsOverwritten()
            ->count();

        if ($count === 1) {
            return 'Achtung es wird ein Feld in dem anderen Formular gelöscht!';
        }

        return 'Achtung es werden ' . $count . ' Felder in den anderen Formularen gelöscht!'; //ToDo Translate
    }
}
