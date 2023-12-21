<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\RelationManagers;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GeneralFieldFormRelationManager extends RelationManager
{
    protected static string $relationship = 'generalFieldForms';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('custom_form_identifier')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.raw_name"))
                    ->required()
                    ->options(function ($livewire){
                        $generalField = $livewire->getOwnerRecord();
                        $selectedIdentifyers= $generalField->generalFieldForms->map(fn(GeneralFieldForm $fieldForm) => $fieldForm->custom_form_identifier);
                        $notSelecdetForms = collect(config("ffhs_custom_forms.forms"))
                            ->filter(
                                fn($class) => $selectedIdentifyers
                                    ->filter(fn($identifier)=> ($class)::identifier() == $identifier)
                                    ->isEmpty()
                            );
                        $keys = $notSelecdetForms->map(fn($class) => ($class)::identifier())->toArray();
                        $values = $notSelecdetForms->map(fn($class) => ($class)::displayName())->toArray();
                        return array_combine($keys,$values);
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('custom_form_identifier_name')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.display_name"))
                    ->state(fn(GeneralFieldForm $record) => ($record->dynamicFormConfiguration())::displayName()),
                Tables\Columns\TextColumn::make('custom_form_identifier')
                    ->label(__("filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.raw_name")),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label(__("filament-package_ffhs_custom_forms::custom_forms.functions.connect")), //ToDo Translate
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function canEdit(Model $record): bool {
        return false;
    }


}
