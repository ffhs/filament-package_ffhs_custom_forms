<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;

class ListGeneralField extends ListRecords
{
    //use Translatable; ToDo Readd

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

        foreach (CustomForms::getFormConfigurations() as $formConfiguration) {
            /**@var CustomFormConfiguration $formConfiguration */
            $identifier = $formConfiguration::identifier();
            $tabs[$identifier] = Tab::make($formConfiguration::displayName())
                ->badge($this->prepareTabQuery($identifier, GeneralField::query())->count())
                ->modifyQueryUsing(fn($query) => $this->prepareTabQuery($identifier, $query));
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
//            LocaleSwitcher::make(), ToDo Readd
            CreateAction::make(),
        ];
    }

    protected function prepareTabQuery($identifier, $query)
    {
        return $query->whereIn(
            'id',
            GeneralFieldForm::query()
                ->where('custom_form_identifier', $identifier)
                ->select('general_field_id')
        );
    }
}
