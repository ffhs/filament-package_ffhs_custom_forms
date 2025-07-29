<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\CreateCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ListCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ViewCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomFormAnswerResource extends Resource
{
    protected static ?string $model = CustomFormAnswer::class;
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name_' . app()->getLocale();
    }

    public static function getNavigationGroup(): ?string
    {
        return CustomFormAnswer::__('navigation.group');
    }

    public static function getNavigationParentItem(): ?string
    {
        $title = CustomFormAnswer::__('navigation.parent');

        return empty($title) ? null : $title;
    }

    public static function getNavigationLabel(): string
    {
        return CustomFormAnswer::__('label.multiple');
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return CustomFormAnswer::__('label.multiple');
    }

    public static function getTitleCaseModelLabel(): string
    {
        return CustomFormAnswer::__('label.single');
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && static::can('showResource');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn($record) => static::getUrl('edit', [$record]))
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('short_title')
                    ->label(CustomFormAnswer::__('attributes.short_title')),
                TextColumn::make('customForm.short_title')
                    ->label(
                        CustomForm::__('label.single') . ' ' . CustomForm::__('attributes.short_title')
                    ),
                TextColumn::make('customForm.custom_form_identifier')
                    ->label(CustomForm::__('attributes.custom_form_identifier'))
                    ->state(fn(CustomFormAnswer $record) => $record
                        ->customForm
                        ->dynamicFormConfiguration()::displayName()
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


    public static function resolveRecordRouteBinding(string|int $key, ?Closure $modifyQuery = null): ?Model
    {
        $query = static::getRecordRouteBindingEloquentQuery();

        if ($modifyQuery) {
            $query = $modifyQuery($query) ?? $query;
        }

        return app(static::getModel())
            ->resolveRouteBindingQuery($query, $key, static::getRecordRouteKeyName())
            ->with(['customForm', 'customFieldAnswers'])
            ->first();
    }
}
