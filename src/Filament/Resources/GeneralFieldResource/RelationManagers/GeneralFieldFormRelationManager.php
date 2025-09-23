<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\RelationManagers;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GeneralFieldFormRelationManager extends RelationManager
{
    protected static string $relationship = 'generalFieldForms';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            CustomFormTypeSelector::make()
                ->required(),
            Group::make([
                Toggle::make('is_required')
                    ->label(GeneralFieldForm::__('attributes.is_required'))
                    ->default(false),
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
                    ->label(GeneralFieldForm::__('attributes.custom_form_identifier_name.label'))
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
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
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
