<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Helper\EditCustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomForm extends EditRecord
{
    protected static string $resource = CustomFormResource::class;


    public function getMaxContentWidth(): MaxWidth|string|null {
        return MaxWidth::Full;
    }

    public function form(Form $form): Form {
        return $form->schema([Section::make()->schema([CustomFormEditor::make()])]);
    }

    protected function fillForm(): void {
        $this->form->fill(EditCustomFormLoadHelper::load($this->getRecord()));
    }


    public function getTitle(): string|Htmlable {
        return $this->record->short_title . " - " . parent::getTitle();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }






}
