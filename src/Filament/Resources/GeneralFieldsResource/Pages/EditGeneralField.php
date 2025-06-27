<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Contracts\Support\Htmlable;

class EditGeneralField extends EditRecord
{
    use Translatable;
    use HasGeneralFieldForm;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return trans(GeneralField::__('pages.edit.title'), ['name' => $this->getRecord()->name]);
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                $this->getGeneralFieldBasicSettings(),
                $this->getOverwriteTypeOptions(),
                $this->getGeneralFieldTypeOptions(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!array_key_exists('overwrite_option_keys', $data)) {
            return parent::mutateFormDataBeforeSave($data);
        }

        $names = $data['overwrite_option_keys'];
        $overwriteOptions = $data['overwrite_options'];
        $allowedOverwriteOptions = [];

        foreach ($names as $name) {
            $allowedOverwriteOptions[$name] = $overwriteOptions[$name] ?? null;
        }

        unset($data['overwrite_option_keys']);
        $data['overwrite_options'] = $allowedOverwriteOptions;

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (array_key_exists('overwrite_options', $data)) {
            $data['overwrite_option_keys'] = array_keys($data['overwrite_options'] ?? []);
        }
        return parent::mutateFormDataBeforeFill($data);
    }


    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}
