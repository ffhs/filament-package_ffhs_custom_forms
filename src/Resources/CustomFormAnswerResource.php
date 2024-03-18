<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\CreateCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\ListCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormAnswerResource\Pages\ViewCustomFormAnswer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomFormAnswerResource extends Resource
{
    protected static ?string $model = CustomFormAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';



    const langPrefix= "filament-package_ffhs_custom_forms::custom_forms.fields.";

    public static function getRecordTitleAttribute(): ?string {
        return "name_" . app()->getLocale();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.custom_form_answer');
    }

    public static function getNavigationParentItem(): ?string {
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
                Tables\Columns\TextColumn::make("id"),
                Tables\Columns\TextColumn::make("short_title")
                    ->label("Name"), //ToDo Translate
                Tables\Columns\TextColumn::make("customForm.short_title")
                    ->label("Formular Name"), //ToDo Translate,
                Tables\Columns\TextColumn::make('customForm.custom_form_identifier')
                    ->label("Formular Art") //ToDo Translate,
                    ->state(fn(CustomFormAnswer $record) =>($record->customForm->dynamicFormConfiguration())::displayName()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomFormAnswer::route('/'),
            'create' => CreateCustomFormAnswer::route('/create'),
            'edit' => EditCustomFormAnswer::route('/{record}/edit'),
            'view'=> ViewCustomFormAnswer::route('/{record}')
        ];
    }
}
