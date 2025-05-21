<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\EditHelper\EditCustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomForm extends EditRecord
{
    use Translatable;

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
        return $form->schema([Section::make()->schema([CustomFormEditor::make()])]);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->short_title . ' - ' . parent::getTitle();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        EditCustomFormSaveHelper::save($this->data, $this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    protected function fillForm(): void
    {
        $this->form->fill(EditCustomFormLoadHelper::load($this->getRecord()));
    }

    protected function getHeaderActions(): array
    {
        return [
            CustomFormSchemaExportAction::make(),
            CustomFormSchemaImportAction::make()
                ->existingForm(fn(CustomForm $record) => $record)
                ->disabled(fn(CustomForm $record) => $record->ownedFields->count() != 0 ||
                    $record->rules->count() != 0
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
