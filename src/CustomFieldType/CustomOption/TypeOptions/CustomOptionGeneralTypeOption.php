<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\TypeOptions;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldsResource\Pages\EditGeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class CustomOptionGeneralTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    public function getDefaultValue(): array
    {
        return [];
    }

    public function getComponent(string $name): Component
    {
        return Repeater::make($name)
            ->collapseAllAction(fn($action) => $action->hidden())
            ->expandAllAction(fn($action) => $action->hidden())
            ->itemLabel(fn($state, $record) => $state['name'])
            ->visible(fn($livewire) => $livewire instanceof EditGeneralField)
            ->label(CustomOption::__('label.multiple'))
            ->columnSpanFull()
            ->collapsible()
            ->collapsed()
            ->addable()
            ->columns()
            ->relationship('customOptions')
            ->afterStateUpdated(function ($set, array $state) use ($name) {
                foreach (array_keys($state) as $optionKey) {
                    if (empty($state[$optionKey]['identifier'])) {
                        $state[$optionKey]['identifier'] = uniqid();
                    }
                }
                $set($name, $state);
            })->schema(fn($record) => once(fn() => [
                TextInput::make('name')
                    ->label(CustomOption::__('name.label'))
                    ->helperText(CustomOption::__('identifier.helper_text'))
                    ->required(),
                TextInput::make('identifier')
                    ->label(CustomOption::__('identifier.label'))
                    ->helperText(CustomOption::__('identifier.helper_text'))
                    ->required(),
            ]));
    }
}
