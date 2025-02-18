<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\RelationManagers;

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


    //To Do set required and max
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('custom_form_identifier')
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.raw_name'))
                    ->required()
                    ->options(function ($livewire) {
                        $generalField = $livewire->getOwnerRecord();
                        $selectedIdentifyers = $generalField->generalFieldForms->map(
                            fn(GeneralFieldForm $fieldForm) => $fieldForm->custom_form_identifier
                        );
                        $notSelecdetForms = collect(config('ffhs_custom_forms.forms'))
                            ->filter(
                                fn($class) => $selectedIdentifyers
                                    ->filter(fn($identifier) => ($class)::identifier() == $identifier)
                                    ->isEmpty()
                            );
                        $keys = $notSelecdetForms->map(fn($class) => ($class)::identifier())->toArray();
                        $values = $notSelecdetForms->map(fn($class) => ($class)::displayName())->toArray();
                        return array_combine($keys, $values);
                    }),
                Forms\Components\Group::make([
                    Forms\Components\Toggle::make('is_required')
                        ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.is_required'))
                        ->default(true),
                    Forms\Components\Toggle::make('export')
                        ->label("Wird exportiert") //ToDo Translate
                        ->default(false),
                ])
            ]);
    }

    //To Do show required and max
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('custom_form_identifier_name')
                    ->label(
                        __('filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.display_name')
                    )
                    ->state(fn(GeneralFieldForm $record) => ($record->dynamicFormConfiguration())::displayName()),
                /* Tables\Columns\IconColumn::make('is_required')
                     ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.is_required'))
                     ->boolean(),
                 Tables\Columns\IconColumn::make('export')
                     ->label("Wird exportiert")//ToDo Translate
                     ->boolean(),*/
                Tables\Columns\CheckboxColumn::make('is_required')
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.is_required')),
                Tables\Columns\CheckboxColumn::make('export')
                    ->label("Wird exportiert"),//ToDo Translate
                /*
                Tables\Columns\TextColumn::make('custom_form_identifier')
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.form.custom_form_identifier.raw_name')),
                 */
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.functions.connect')), //ToDo Translate
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

    protected function canEdit(Model $record): bool
    {
        return false; //toDo Check if it is possible and required
    }


}
