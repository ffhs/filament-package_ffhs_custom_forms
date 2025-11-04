<?php


namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Tables;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomFormAnswerTable
{
    public static function configure(Table $table): table
    {
        return $table
            ->recordUrl(fn($record) => CustomFormAnswerResource::getUrl('edit', [$record]))
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('short_title')
                    ->label(CustomFormAnswer::__('attributes.short_title')),
                TextColumn::make('customForm.short_title')
                    ->label(
                        CustomForm::__('label.single') . ' ' . CustomForm::__('attributes.short_title.label')
                    ),
                TextColumn::make('customForm.custom_form_identifier.label')
                    ->label(CustomForm::__('attributes.custom_form_identifier.label'))
                    ->state(fn(CustomFormAnswer $record) => $record
                        ->customForm
                        ->dynamicFormConfiguration()::displayname()
                    ),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
