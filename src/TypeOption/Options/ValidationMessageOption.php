<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Exception;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Component;

class ValidationMessageOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = [];

    public function getComponent(string $name): Component
    {
        return Repeater::make($name)
            ->label(TypeOption::__('validation_messages.label'))
            ->helperText(TypeOption::__('validation_messages.helper_text'))
            ->schema([
                TextInput::make('rule')
                    ->label(TypeOption::__('validation_messages.rule.label'))
                    ->helperText(TypeOption::__('validation_messages.rule.helper_text'))
                    ->datalist($this->getValidationMessageParameters(...))
                    ->required(),
                TextInput::make('message')
                    ->label(TypeOption::__('validation_messages.message.label'))
                    ->helperText(TypeOption::__('validation_messages.message.helper_text'))
                    ->columnSpan(2)
                    ->nullable(),
            ])
            ->addActionLabel(TypeOption::__('validation_messages.add_label'))
            ->collapsible(false)
            ->columns(3)
            ->columnSpanFull();
    }

    public function modifyFormComponent(Component|Field $component, mixed $value): Component
    {
        if (!$component instanceof Field) {
            return $component;
        }

        if (!is_array($value)) {
            $value = [];
        }

        $validationMessages = collect($value)
            ->filter(fn($item) => is_array($item) && isset($item['rule'], $item['message']))
            ->mapWithKeys(fn($item) => [$item['rule'] => $item['message']]);

        return $component->validationMessages($validationMessages->toArray());
    }

    protected function getValidationMessageParameters(Get $get): array
    {
        try {
            $temporaryField = new CustomField();
            $temporaryField->fill($get('../../../'));

            /** @var Field $schemaComponent */
            $schemaComponent = $temporaryField
                ->getType()
                ->getFormComponent($temporaryField, 'default', ['renderer' => fn() => []]);

//            $schema = Schema::make(new EditRecord()); ToDo check if its need i dont think so
//            $schemaComponent->container($schema);

            return $this->extractValidationRules($schemaComponent);
        } catch (Exception $exception) {
            return [];
        }
    }

    private function extractValidationRules(Field $component): array
    {
        $excludedRules = ['nullable', 'array'];

        return collect($component->getValidationRules())
            ->map(function ($rule) {
                if (!is_string($rule)) {
                    return null;
                }

                return explode(':', $rule)[0] ?? null;
            })
            ->filter(fn($ruleName) => $ruleName !== null && !in_array($ruleName, $excludedRules, true))
            ->unique()
            ->values()
            ->toArray();
    }
}
