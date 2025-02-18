<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\ListTemplate;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListCustomForm extends ListRecords
{
    protected const langPrefix = 'filament-package_ffhs_custom_forms::custom_forms.form.';
    protected static string $resource = CustomFormResource::class;

    public function getTabs(): array
    {
        $query = static::$resource::getEloquentQuery();

        $tabs = [
            'all' => Tab::make("All")
                ->badge($query->clone()->count()),
        ];

        foreach (config('ffhs_custom_forms.forms') as $formClass) {
            $tabs[$formClass::identifier()] =
                Tab::make($formClass::displayName())
                    ->badge($query->where("custom_form_identifier", $formClass::identifier())->count())
                    ->modifyQueryUsing(fn($query) => $this->prepareTabQuery($formClass::identifier(), $query));
        }

        return $tabs;
    }

    private function prepareTabQuery($identifier, Builder $query): Builder
    {
//        dd($query->toRawSql());
        $query = $query->where("custom_form_identifier", $identifier);
        return $query;
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return "all";
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('template_identifier') //ToDo
                ->visible($this instanceof ListTemplate)
                    ->label('Template Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('short_title')
                    ->label(__(self::langPrefix . 'short_title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('custom_form_identifier')
                    ->label(__(self::langPrefix . 'custom_form_identifier.display_name'))
                    ->state(fn(CustomForm $record) => ($record->dynamicFormConfiguration())::displayName())
                    ->sortable(),
                TextColumn::make('owned_fields_count')
                    ->label(__(self::langPrefix . 'owned_fields_amount')),
                //TextColumn::make('custom_fields_count')
                //    ->label(__(self::langPrefix . 'custom_fields_amount'))
            ])
            ->defaultSort('short_title')
            ->paginated([50])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withCount(['ownedFields'])
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            CustomFormSchemaImportAction::make()->link(),
            CreateAction::make(),
        ];
    }

}
