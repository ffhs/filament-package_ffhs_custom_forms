<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Error;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Resources\Pages\EditRecord;

class ValidationMessageOption extends TypeOption
{
    public function getDefaultValue(): array
    {
        return [];
    }

    public function getComponent(string $name): FormsComponent
    {
        return Repeater::make($name)
            ->label(TypeOption::__('validation_messages.label'))
            ->helperText(TypeOption::__('validation_messages.helper_text'))
            ->schema([
                TextInput::make('rule')
                    ->label(TypeOption::__('validation_messages.rule.label'))
                    ->helperText(TypeOption::__('validation_messages.rule.helper_text'))
                    ->required()
                    ->datalist($this->getValidationMessageParameters(...)),
                TextInput::make('message')
                    ->label(TypeOption::__('validation_messages.message.label'))
                    ->helperText(TypeOption::__('validation_messages.message.helper_text'))
                    ->nullable()
                    ->datalist($this->getValidationMessageParameters(...))
                    ->columnSpan(2),
            ])
            ->addActionLabel(TypeOption::__('validation_messages.add_label'))
            ->collapsible(false)
            ->columns(3)
            ->columnSpanFull();
    }

    public function modifyFormComponent(FormsComponent|Field $component, mixed $value): FormsComponent
    {
        if (!is_array($value)) {
            $value = [];
        }

        $value = collect($value)->mapWithKeys(fn($information) => [$information['rule'] => $information['message']]);

        return $component->validationMessages($value->toArray());
    }

    public function modifyInfolistComponent(InfolistsComponent $component, mixed $value): InfolistsComponent
    {
        return $component;
    }

    protected function getValidationMessageParameters(Get $get): array
    {
        try {
            $temporaryField = new CustomField();
            $temporaryField->fill($get('../../../'));
            $rules = $temporaryField
                ->getType()
                ->getFormComponent($temporaryField, $temporaryField->customForm, 'default', ['renderer' => fn() => []])
                ->container(ComponentContainer::make(new EditRecord()))
                ->getValidationRules();

            return collect($rules)
                ->map(fn($rule) => is_string($rule) ? explode(':', $rule)[0] ?? null : null)
                ->unique()
                ->filter(fn($preparedValue) => !in_array($preparedValue, ['nullable', 'array', null,], false))
                ->toArray();
        } catch (Error $error) {
            return [];
        }
    }
}
