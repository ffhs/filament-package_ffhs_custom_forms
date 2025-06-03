<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedAnswerInfolist;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomFormAnswer extends ViewRecord
{
    protected static string $resource = CustomFormAnswerResource::class;
    protected static ?string $title = 'Formular Anschauen'; //ToDo Translate

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        EmbeddedAnswerInfolist::make()
                            ->autoViewMode()
                            ->columnSpanFull()
                    ])
            ]);
    }
}
