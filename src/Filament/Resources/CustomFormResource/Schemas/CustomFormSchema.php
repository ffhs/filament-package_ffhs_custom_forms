<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Schemas;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DefaultFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormTypeSelector;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomFormSchema
{
    public static function configure(Schema $schema, bool $template = false): Schema
    {
        return $schema
            ->components([
                TextInput::make('template_identifier') //ToDo Make it work in edit
                ->visible($template)
                    ->label(CustomForm::__('attributes.template_identifier.label'))
                    ->helperText(CustomForm::__('attributes.template_identifier.helper_text'))
                    ->hiddenOn('edit')
                    ->required()
                    ->unique(),
                TextInput::make('short_title') //ToDo Make it work in edit
                ->label(CustomForm::__('attributes.short_title.label'))
                    ->helperText(CustomForm::__('attributes.short_title.helper_text'))
                    ->hiddenOn('edit')
                    ->required(),
                CustomFormTypeSelector::make() //ToDo Make it work in edit
                ->hiddenOn('edit')
                    ->required(),
                CustomFormEditor::make('custom_form')
                    ->hiddenOn('create')
                    ->hiddenLabel()
                    ->formConfiguration(function (?CustomForm $record) {
                        return $record?->getFormConfiguration() ?? DefaultFormConfiguration::make();
                    })
            ]);
    }
}
