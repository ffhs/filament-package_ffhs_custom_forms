<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Schemas;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CustomFormAwareSchema
{
    public static function configure(Schema $schema, bool $template = false): Schema
    {
        return $schema->schema([
            TextInput::make('short_title')
                ->label(CustomFormAnswer::__('attributes.short_title'))
                ->hiddenOn('edit'),
            Select::make('custom_form_id')
                ->label(CustomForm::__('label.single'))
                ->hiddenOn('edit')
                ->relationship(
                    'customForm',
                    'short_title',
                    fn(Builder $query) => $query->whereNull('template_identifier')
                )
                ->required(),

            EmbeddedCustomForm::make('form_answer')
                ->hiddenOn('create')
                ->autoViewMode()
                ->columnSpanFull()
        ]);

        //todo add fill_out on start without record.
    }
}
