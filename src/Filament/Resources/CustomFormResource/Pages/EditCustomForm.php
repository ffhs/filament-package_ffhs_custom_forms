<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormEditor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveCustomFormEditorData;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use function Filament\Support\is_app_url;

class EditCustomForm extends EditRecord
{
    use Translatable;
    use CanSaveCustomFormEditorData;
    use CanLoadCustomFormEditorData;

    protected static string $resource = CustomFormResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return static::$resource::canAccess()
            && static::$resource::can('manageForms');
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            CustomFormEditor::make('custom_form')
                ->label('')
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->short_title . ' - ' . parent::getTitle();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();
        $this->saveCustomFormEditorData($this->data['custom_form'], $this->getRecord());

        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'custom_form' => $this->loadCustomFormEditorData($this->getRecord())
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CustomFormSchemaExportAction::make(),
            CustomFormSchemaImportAction::make()
                ->existingForm(fn(CustomForm $record) => $record)
                ->disabled(fn(CustomForm $record) => $record->ownedFields->count() > 0 || $record->rules->count() > 0
                )
                ->action(function (CustomFormSchemaImportAction $action, $data) {
                    $action->callImportAction($data);
                    $action->redirect('edit');
                }),
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}
