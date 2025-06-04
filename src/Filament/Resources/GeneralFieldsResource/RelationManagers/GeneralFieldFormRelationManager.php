<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\RelationManagers;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GeneralFieldFormRelationManager extends RelationManager
{
    protected static string $relationship = 'generalFieldForms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('custom_form_identifier')
                    ->label(GeneralFieldForm::__('attributes.custom_form_identifier'))
                    ->required()
                    ->options(function ($livewire) {
                        $generalField = $livewire->getOwnerRecord();
                        $selectedIdentifiers = $generalField
                            ->generalFieldForms
                            ->map(fn(GeneralFieldForm $fieldForm) => $fieldForm->custom_form_identifier);

                        return collect(CustomForms::getFormConfigurations())
                            ->filter(function (CustomFormConfiguration $configuration) use ($selectedIdentifiers) {
                                return $selectedIdentifiers->contains($configuration);
                            })
                            ->mapWithKeys(fn(CustomFormConfiguration $configuration) => [
                                $configuration::identifier() => $configuration::displayName()
                            ]);
                    }),
                Group::make([
                    Toggle::make('is_required')
                        ->label(GeneralFieldForm::__('attributes.is_required'))
                        ->default(true),
                    Toggle::make('export')
                        ->label(GeneralFieldForm::__('attributes.export'))
                        ->default(false),
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('custom_form_identifier_name')
                    ->label(GeneralFieldForm::__('attributes.custom_form_identifier'))
                    ->state(fn(GeneralFieldForm $record) => ($record->dynamicFormConfiguration())::displayName()),
                CheckboxColumn::make('is_required')
                    ->label(GeneralFieldForm::__('attributes.is_required')),
                CheckboxColumn::make('export')
                    ->label(GeneralFieldForm::__('attributes.export')),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label(GeneralFieldForm::__('actions.connect')),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function canEdit(Model $record): bool
    {
        return false;
    }
}
