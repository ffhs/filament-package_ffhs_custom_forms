<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class CreateTemplate extends CreateCustomForm
{
    protected static string $resource = TemplateResource::class;

    public function getTitle(): string|Htmlable
    {
        return CustomForm::__('pages.create_template.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('template_identifier')
                    ->label('Template Id')
                    ->required(), //ToDo Translate
                TextInput::make('short_title')
                    ->label('Namen')
                    ->required(), //ToDo Translate
                Select::make('custom_form_identifier')
                    ->label('Formularart') //ToDo Translate
                    ->required()
                    ->options(function () {
                        $keys = array_map(
                            fn($config) => $config::identifier(),
                            config('ffhs_custom_forms.forms')
                        );
                        $values = array_map(
                            fn($config) => $config::displayName(),
                            config('ffhs_custom_forms.forms')
                        );

                        return array_combine($keys, $values);
                    }),
            ]);
    }
}
