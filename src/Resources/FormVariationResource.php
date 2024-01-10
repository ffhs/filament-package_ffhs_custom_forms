<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Resources;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource\Pages\CreateFormVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource\Pages\EditFormVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\FormVariationResource\Pages\ListFormVariations;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormVariationResource extends Resource
{
        protected static ?string $model = FormVariation::class;

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


        public static function getRecordTitleAttribute(): ?string {
            return "short_title";
        }

        public static function getNavigationGroup(): ?string
        {
            return __('filament-package_ffhs_custom_forms::custom_forms.navigation.group.forms');
        }

        public static function getNavigationLabel(): string
        {
            return "Formular Variationen";//ToDo Translate
        }

        public static function getNavigationParentItem(): ?string {
            return __('filament-package_ffhs_custom_forms::custom_forms.navigation.forms');
        }

    /*ToDo
      public static function getTitleCasePluralModelLabel(): string {
        return __('filament-package_ffhs_custom_forms::custom_forms.navigation.general_fields');
         }

      public static function getTitleCaseModelLabel(): string {
          return __('filament-package_ffhs_custom_forms::custom_forms.fields.general_field');
      }
   */
        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Section::make()
                        ->columns()
                    ->schema([
                        TextInput::make("short_title")
                            ->label("Title") //ToDo Translate
                            ->required(),

                        Select::make("custom_form_id")
                            ->columnStart(1)
                            ->required()
                            ->relationship("customForm", "short_title", fn(Builder$query)=>
                                $query ->whereIn("custom_form_identifier",
                                    collect(config("ffhs_custom_forms.forms"))
                                        ->filter(fn($configClass)=> $configClass::variationModel() == FormVariation::class)
                                        ->map(fn($configClass)=> $configClass::identifier())
                                )
                            ),

                        Toggle::make("is_disabled")
                            ->label("Disabled")//ToDo Translate
                            ->columnStart(1),

                        Toggle::make("is_hidden")
                            ->label("Versteckt") //ToDo Translate
                            ->columnStart(1),

                    ])
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->filters([ //ToDo Why it isnt There
                    SelectFilter::make("custom_form_id")
                        ->relationship("customForm", "short_title")
                        ->label("Formular") //ToDo Translate
                ], Tables\Enums\FiltersLayout::AboveContent)
                ->columns([
                    Tables\Columns\TextColumn::make("id")
                        ->label("Id")//ToDo Translate,
                        ->sortable(),
                    Tables\Columns\TextColumn::make("short_title")
                        ->label("Name") //ToDo Translate
                        ->searchable(),
                    Tables\Columns\TextColumn::make("customForm.short_title")
                        ->label("Formular") //ToDo Translate,
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\IconColumn::make("is_disabled")
                        ->label("Deaktiviert") //ToDo Translate,
                        ->boolean(),
                    Tables\Columns\IconColumn::make("is_hidden")
                        ->label("Versteckt") //ToDo Translate,
                        ->boolean(),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->with("customForm");
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
            'index' => ListFormVariations::route('/'),
            'create' => CreateFormVariation::route('/create'),
            'edit' => EditFormVariation::route('/{record}/edit'),
        ];
    }
}
