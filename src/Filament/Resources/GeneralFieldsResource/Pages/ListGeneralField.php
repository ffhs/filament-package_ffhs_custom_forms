<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListGeneralField extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = GeneralFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make("All")
                ->badge(GeneralField::query()->count()),
        ];

        foreach (config("ffhs_custom_forms.forms") as $formClass) {
            $tabs[$formClass::identifier()] =
                Tab::make($formClass::displayName())
                    ->badge($this->prepareTabQuerry($formClass::identifier(), GeneralField::query())->count())
                    ->modifyQueryUsing(fn($query) => $this->prepareTabQuerry($formClass::identifier(), $query));
        }


        return $tabs;
    }

    private function prepareTabQuerry($identifier, $query)
    {
        return $query->whereIn(
            "id",
            GeneralFieldForm::query()
                ->where("custom_form_identifier", $identifier)
                ->select("general_field_id")
        );
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return "all";
    }

}
