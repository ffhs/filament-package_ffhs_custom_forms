<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedAnswerInfolist;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

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
        return $schema->schema([
            EmbeddedAnswerInfolist::make()
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
