<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditGeneralField extends EditRecord
{
    use Translatable;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return trans(GeneralField::__('pages.edit.title'), ['name' => $this->getRecord()->name ?? '']);
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
