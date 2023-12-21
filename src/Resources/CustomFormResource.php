<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldsResource\Pages\{CreateGeneralField,ListGeneralField,EditGeneralField};
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\GeneralFieldResource\Pages\ListCustomFormField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class CustomFormResource extends Resource
{


    protected static ?string $model = CustomForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';


    public static function getNavigationGroup(): ?string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
    }
    public static function getNavigationLabel(): string
    {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
    }


    public static function form(Form $form): Form
    {
        return $form;
    }


    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id"),
                Tables\Columns\TextColumn::make("short_title"),
                Tables\Columns\TextColumn::make('generalFieldForms.custom_form_identifier')
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.form_connections'))
                    ->state(fn(CustomForm $record) =>($record->dynamicFormConfiguration())::displayName()),
                Tables\Columns\TextColumn::make("customFields")
                    ->state(fn(CustomForm $record) => $record->customFields->count())
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
            'index' => ListCustomFormField::route('/'),
        ];
    }
}
