<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\ListCustomForm;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomFormResource extends Resource
{
    use Translatable;

    protected static ?string $model = CustomForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    const langPrefix= "filament-package_ffhs_custom_forms::custom_forms.form.";

    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }
    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }


    public static function getTitleCasePluralModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }

    public static function getTitleCaseModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.form.custom_form');
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->where("is_template", false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->bulkActions([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->columns([
                Tables\Columns\TextColumn::make("id"),
                Tables\Columns\TextColumn::make("short_title")
                    ->label(__(self::langPrefix . "short_title")),
                Tables\Columns\TextColumn::make('custom_form_identifier')
                    ->label(__(self::langPrefix . "custom_form_identifier.display_name"))
                    ->state(fn(CustomForm $record) =>($record->dynamicFormConfiguration())::displayName()),
                Tables\Columns\TextColumn::make("custom_fields_amount")
                    ->label(__(self::langPrefix . "custom_fields_amount"))
                    ->state(fn(CustomForm $record) => $record->ownedFields->count()),
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
            'index' => ListCustomForm::route('/'),
            'create' => CreateCustomForm::route('/create'),
            'edit' => EditCustomForm::route('/{record}/edit'),
        ];
    }
}
