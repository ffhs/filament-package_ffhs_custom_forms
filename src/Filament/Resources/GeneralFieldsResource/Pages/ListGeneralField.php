<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Illuminate\Contracts\Support\Htmlable;

class ListGeneralField extends ListRecords
{
    //use Translatable;;

    protected static string $resource = GeneralFieldResource::class;

    public function getTitle(): string|Htmlable
    {
        return GeneralField::__('pages.list.title');
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->badge(GeneralField::query()->count()),
        ];

        foreach (config('ffhs_custom_forms.forms') as $formClass) {
            $tabs[$formClass::identifier()] = Tab::make($formClass::displayName())
                ->badge($this->prepareTabQuerry($formClass::identifier(), GeneralField::query())->count())
                ->modifyQueryUsing(fn($query) => $this->prepareTabQuerry($formClass::identifier(), $query));
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }

    private function prepareTabQuerry($identifier, $query)
    {
        return $query->whereIn(
            'id',
            GeneralFieldForm::query()
                ->where('custom_form_identifier', $identifier)
                ->select('general_field_id')
        );
    }
}
