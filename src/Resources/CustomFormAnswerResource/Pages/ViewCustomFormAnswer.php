<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomFormAnswerView;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomFormAnswer extends ViewRecord
{
    protected static string $resource = CustomFormAnswerResource::class;
    protected static ?string $title = 'Formular Anschauen'; //ToDo Translate


    public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        EmbeddedCustomFormAnswerView::make(fn(CustomFormAnswer $record)=>$record)
                            ->autoViewMode()
                            ->columnSpanFull()
                    ])
        ]);
    }


}
