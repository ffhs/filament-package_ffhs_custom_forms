<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource\Tables;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GeneralFieldTable
{
    public static function configure(Table $table): table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label(GeneralField::__('attributes.icon.label'))
                    ->icon(fn($state) => $state),
                TextColumn::make('name')
                    ->searchable()
                    ->label(GeneralField::__('attributes.name.label')),
                TextColumn::make('type')
                    ->label(GeneralField::__('attributes.type.label'))
                    ->searchable()
                    ->getStateUsing(fn(GeneralField $record) => $record->getType()->displayname()),
                TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(GeneralField::__('attributes.form_connections.label'))
                    ->listWithLineBreaks()
                    ->searchable()
                    ->state(fn(GeneralField $record) => $record->generalFieldForms
                        ->map(fn($generalFieldForm) => $generalFieldForm->dynamicFormConfiguration())
                        ->map(fn(CustomFormConfiguration $class) => ($class)::displayName())
                    ),
                ToggleColumn::make('is_active')
                    ->label(GeneralField::__('attributes.is_active.label')),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }

}
