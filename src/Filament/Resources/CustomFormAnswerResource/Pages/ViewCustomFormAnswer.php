<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\CustomFormAnswerEntry;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @method CustomFormAnswer getRecord()
 */
class ViewCustomFormAnswer extends ViewRecord
{
    use CanLoadFormAnswer;

    protected static string $resource = CustomFormAnswerResource::class;
    protected static ?string $title = 'Formular Anschauen'; //ToDo Translate

    public function getTitle(): string|Htmlable
    {
        $attributes = $this
            ->getRecord()
            ->attributesToArray();

        return trans(CustomFormAnswer::__('pages.view.title'), $attributes);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                CustomFormAnswerEntry::make('custom_form_answer')
                    ->state($this->loadCustomAnswerForEntry($this->getRecord()))
                    ->customForm(fn(CustomFormAnswer $record) => $record->customForm)
                    ->autoViewMode()
                    ->columnSpanFull()
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            EditAction::make(),
        ];
    }
}
