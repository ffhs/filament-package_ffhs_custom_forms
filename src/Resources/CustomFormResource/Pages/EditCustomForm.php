<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormExporter\SchemaExporter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
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


    public function getMaxContentWidth(): MaxWidth|string|null {
        return MaxWidth::Full;
    }

    public function form(Form $form): Form {
        return $form->schema([Section::make()->schema([CustomFormEditor::make()])]);
    }

    public function getTitle(): string|Htmlable {
        return $this->record->short_title . " - " . parent::getTitle();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void {

        $this->authorizeAccess();

        EditCustomFormSaveHelper::save($this->data, $this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);

    }

    protected function fillForm(): void {
       $this->form->fill(EditCustomFormLoadHelper::load($this->getRecord()));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make("export_field")->action(fn($record) => dd(FormSchemaExporter::make()->export($record)))
        ];
    }


}
