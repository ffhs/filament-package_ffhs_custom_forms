<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewCustomFormAnswer extends ViewRecord
{
    protected static string $resource = CustomFormAnswerResource::class;
    protected static ?string $title = 'Formular Anschauen'; //ToDo Translate


    public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema(fn(CustomFormAnswer$record) => CustomFormRender::generateInfoListSchema($record, $record->customForm->getFormConfiguration()::displayViewMode()));
    }


}
