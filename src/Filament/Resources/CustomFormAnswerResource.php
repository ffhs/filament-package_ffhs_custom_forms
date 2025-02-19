<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\CreateCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ListCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ViewCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomFormAnswerResource extends Resource
{
    public const langPrefix = 'filament-package_ffhs_custom_forms::custom_forms.fields.';
    protected static ?string $model = CustomFormAnswer::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name_' . app()->getLocale();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.custom_form_answer');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }

    /*ToDo Translate

     *     public static function getTitleCasePluralModelLabel(): string {
            return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
        }

        public static function getTitleCaseModelLabel(): string {
            return __('filament-package_ffhs_custom_forms::custom_forms.fields.general_field');
        }
     */

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('short_title')
                    ->label('Name'), //ToDo Translate
                TextColumn::make('customForm.short_title')
                    ->label('Formular Name'), //ToDo Translate,
                TextColumn::make('customForm.custom_form_identifier')
                    ->label('Formular Art') //ToDo Translate,
                    ->state(
                        fn(CustomFormAnswer $record) => ($record->customForm->dynamicFormConfiguration())::displayName()
                    ),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomFormAnswer::route('/'),
            'create' => CreateCustomFormAnswer::route('/create'),
            'edit' => EditCustomFormAnswer::route('/{record}/edit'),
            'view' => ViewCustomFormAnswer::route('/{record}')
        ];
    }
}
