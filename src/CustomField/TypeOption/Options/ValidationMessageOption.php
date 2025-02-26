<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Infolists\Components\Component as InfolistComponent;
use Filament\Resources\Pages\EditRecord;

class ValidationMessageOption extends TypeOption
{
    use TypeOptionPluginTranslate;

    public function getDefaultValue(): array
    {
        return [];
    }

    public function getComponent(string $name): Component
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

    public function modifyFormComponent(Component|Field $component, mixed $value): Component
    {
        if (!is_array($value)) $value = [];
        $value = collect($value)->mapWithKeys(function ($information) {
            return [$information['rule'] => $information['message']];
        });
        return $component->validationMessages($value->toArray());
    }

    public function modifyInfolistComponent(InfolistComponent $component, mixed $value): InfolistComponent
    {
        return $component;
    }

    protected function getValidationMessageParameters(Get $get): array
    {
        try {
            $temporaryField = new CustomField();
            $temporaryField->fill($get('../../../'));
            $rules = $temporaryField->getType()
                ->getFormComponent(
                    $temporaryField,
                    $temporaryField->customForm,
                    'default',
                    ['renderer' => fn() => []]
                )
                ->container(ComponentContainer::make(new EditRecord()))
                ->getValidationRules();

            return collect($rules)
                ->map(function ($rule) {
                    if (!is_string($rule)) return null;
                    return explode(':', $rule)[0] ?? null;
                })
                ->unique()
                ->filter(
                    fn($preparedValue) => !in_array($preparedValue, [
                        'nullable',
                        'array',
                        null,
                    ])
                )
                ->toArray();
        } catch (\Error $error) {
            return [];
        }
    }


}
