<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;

class ViewVariationsFromCustomForm extends ViewRecord
{
    protected static string $resource = CustomFormResource::class;
    protected static ?string $title = 'Variationen Anschauen'; //ToDo Translate


    public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([

                Group::make()
                    ->visible(fn(CustomForm $record)=> $record->getFormConfiguration()::hasVariations())
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([

                       Section::make("Beziehungsmodel") //ToDo Translate
                           ->columnSpan(2)
                           ->columns(3)
                           ->schema([
                               TextEntry::make("id")
                                   ->getStateUsing(fn(CustomForm $record) => $record->relation_model_id)
                                   ->weight(FontWeight::SemiBold)
                                   ->label("Id:") //ToDo Translate
                                   ->columnStart(1)
                                   ->inlineLabel(),

                               TextEntry::make("relation_model")
                                   ->getStateUsing(fn(CustomForm $record) => $record->relation_model_type)
                                   ->weight(FontWeight::SemiBold)
                                   ->label("Model") //ToDo Translate
                                    ->columnStart(1)
                                   ->inlineLabel(),

                           ]),

                       Section::make("Variationen") //ToDo Translate
                       ->columnSpan(2)
                           ->schema([

                               TextEntry::make("variation_model")
                                   ->getStateUsing(fn(CustomForm$record) => $record->getFormConfiguration()::variationModel())
                                   ->weight(FontWeight::SemiBold)
                                   ->label(""),

                               RepeatableEntry::make("variations")
                                   ->grid(2)
                                   ->label("")
                                   ->getStateUsing(fn(CustomForm $record)=>$record->variationModels()->get())
                                   ->schema(function(CustomForm $record){
                                       $customForm = $record;
                                       return  [
                                           TextEntry::make("name")
                                               ->weight(FontWeight::SemiBold)
                                               ->inlineLabel()
                                               ->getStateUsing(
                                                   fn($record) => $customForm->getFormConfiguration()::variationName($record)
                                               ),
                                           TextEntry::make("id")
                                               ->label("id") //ToDo translate
                                               ->weight(FontWeight::SemiBold)
                                               ->inlineLabel()
                                               ->getStateUsing(fn(Model $record) => $record->id),
                                           IconEntry::make("is_disabled")
                                               ->boolean()
                                               ->inlineLabel()
                                               ->label("Deaktiviert") //ToDo translate
                                               ->getStateUsing(
                                                   fn(Model $record) => $customForm->getFormConfiguration()::isVariationDisabled($record)
                                               ),
                                       ];
                                   }),
                           ]),

                    ]),




                TextEntry::make("has_variations")
                    ->size(TextEntrySize::Large)
                    ->label("")
                    ->hidden(fn($record)=>  $record->getFormConfiguration()::hasVariations())
                    ->getStateUsing("Dieses Formular hat keine Variationen") //ToDo Translate

            ]);



    }


}
