<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class CreateCustomFormAnswer extends CreateRecord
{
    protected static string $resource = CustomFormAnswerResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomFormAnswer::__('pages.create.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('short_title')
                ->label(CustomFormAnswer::__('attributes.short_title')),
            Select::make('custom_form_id')
                ->label(CustomForm::__('label.single'))
                ->relationship(
                    'customForm',
                    'short_title',
                    fn(Builder $query) => $query->whereNull('template_identifier')
                )
                ->required(),
        ]);
    }
}
