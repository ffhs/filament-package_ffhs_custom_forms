<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Contracts\ExposesStateToActionData;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
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

    public function form(Schema $schema): Schema
    {
        return $schema->components([
//            CustomFormEditor::make('custom_form')
//                ->label('')
            FormEditor::make('custom_form')
                ->formConfiguration(fn(CustomForm $record) => $record->getFormConfiguration())
                ->hiddenLabel()
        ]);
    }

    /**
     * @throws Throwable
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();
        $this->saveCustomFormEditorData($this->data['custom_form'], $this->getRecord());
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

    public function mountAction(string $name, array $arguments = [], array $context = []): mixed
    {
        $this->mountedActions[] = [
            'name' => $name,
            'arguments' => $arguments,
            'context' => $context,
        ];

        try {
            $action = $this->getMountedAction();
        } catch (ActionNotResolvableException $exception) {
            $action = null;
        }

        if (!$action) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if (($actionComponent = $action->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildSchemas() as $actionComponentChildSchema) {
                $actionComponentChildSchema->validate();
            }
        }

        try {
            if (
                $action->hasAuthorizationNotification() &&
                ($response = $action->getAuthorizationResponseWithMessage())->denied()
            ) {
                $action->sendUnauthorizedNotification($response);

                throw new Cancel;
            }

            $hasSchema = $this->mountedActionHasSchema(mountedAction: $action);

            if ($hasSchema) {
                $action->callBeforeFormFilled();
            }

            $schema = $this->getMountedActionSchema(mountedAction: $action);

            $action->mount([
                'form' => $schema,
                'schema' => $schema,
            ]);

            if ($hasSchema) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if (!$this->mountedActionShouldOpenModal(mountedAction: $action)) {
            return $this->callMountedAction();
        }

        $this->syncActionModals();

        $this->resetErrorBag();

        return null;
    }

    protected function fillForm(): void
    {
        /**@var CustomForm $customForm */
        $customForm = $this->getRecord();
        $customForm->load('ownedRules', 'ownedRules.ruleTriggers', 'ownedRules.ruleEvents');

        $this
            ->form
            ->fill([
                'custom_form' => $this->loadCustomFormEditorData($customForm)
            ]);
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
