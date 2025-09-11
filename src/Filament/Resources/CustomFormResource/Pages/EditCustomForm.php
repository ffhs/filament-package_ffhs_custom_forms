<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;
use function Filament\Support\is_app_url;

class EditCustomForm extends EditRecord
{
    //use Translatable;;
    use CanSaveCustomFormEditorData;
    use CanLoadCustomFormEditorData;

    protected static string $resource = CustomFormResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return static::$resource::canAccess()
            && static::$resource::can('manageForms');
    }

    public function getTitle(): string|Htmlable
    {
        $attributes = $this
            ->getRecord()
            ->attributesToArray();

        return trans(CustomForm::__('pages.edit.title'), $attributes);
    }

    public function getMaxContentWidth(): string|null|Width
    {
        return Width::Full;
    }

    /**
     * @throws Throwable
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();
        $state = $this->form->getState();
        $this->saveCustomFormEditorData($state['custom_form'], $this->getRecord());
        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this
                ->getSavedNotification()
                ?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    protected function fillForm(): void
    {
        /**@var CustomForm $customForm */
        $customForm = $this->getRecord();
        $customForm->load('ownedRules', 'ownedRules.ruleTriggers', 'ownedRules.ruleEvents');

        $this
            ->form
            ->fill(['custom_form' => $this->loadCustomFormEditorData($customForm)]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CustomFormSchemaExportAction::make(),
            CustomFormSchemaImportAction::make()
                ->existingForm(fn(CustomForm $record) => $record)
                ->disabled(fn(CustomForm $record) => $record->ownedFields->count() > 0
                    || $record->rules->count() > 0
                )
                ->action(function (CustomFormSchemaImportAction $action, $data) {
                    $action->callImportAction($data);
                    $action->redirect('edit');
                }),
//            LocaleSwitcher::make(),
//            DeleteAction::make(),
        ];
    }


}
