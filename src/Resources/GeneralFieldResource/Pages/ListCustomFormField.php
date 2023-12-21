<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListCustomFormField extends ListRecords
{
    protected static string $resource = CustomFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array {
        $tabs = [
            'all' => Tab::make("All")
                ->badge(CustomForm::query()->count()),
        ];

        foreach (config("ffhs_custom_forms.forms") as $formClass){
            $tabs[$formClass::identifier()] =
                Tab::make($formClass::displayName());
                    //->badge($this->prepareTabQuerry($formClass::identifier(),GeneralField::query())->count())
                    //->modifyQueryUsing(fn($query) => $this->prepareTabQuerry($formClass::identifier(),$query));

        }

        return $tabs;
    }

    private function prepareTabQuerry ($identifier, $query) {
        return $query/*->whereIn("id",
            GeneralFieldForm::query()
                ->where("custom_form_identifier", $identifier)
                ->select("general_field_id")
        )*/;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return "all";
    }

}
