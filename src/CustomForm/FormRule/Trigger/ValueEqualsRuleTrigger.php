<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\TempCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasBoolCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasNumberCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTextCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class ValueEqualsRuleTrigger extends FormRuleTriggerType
{
    use HasRuleTriggerPluginTranslate;
    use HasTriggerEventFormTargets;
    use HasNumberCheck;
    use HasBoolCheck;
    use HasTextCheck;
    use HasOptionCheck;

    public static function identifier(): string
    {
        return 'value_equals_anchor';
    }

    public function prepareComponent(Component $component, RuleTrigger $trigger): Component
    {
        if ($component instanceof Field) {
            return $component->live(); //ToDo improve
        }

        return $component;
    }

    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        if (!array_key_exists('state', $arguments)) {
            return false;
        }

        if (empty($rule->data)
            || empty($rule->data['target'])
            || empty($rule->data['type'])) {
            return false;
        }

        $targetFieldIdentifier = $rule->data['target'];
        $state = $arguments['state'];
        $targetValue = $state[$targetFieldIdentifier] ?? null;
        $type = $rule->data['type'];

        return match ($type) {
            'number' => $this->checkNumber($targetValue, $rule->data),
            'text' => $this->checkText($targetValue, $rule->data),
            'boolean' => $this->checkBoolean($targetValue, $rule->data),
            'null' => $this->checkNull($targetValue),
            'option' => $this->checkOption($targetValue, $rule->data),
            default => false,
        };
    }

    public function getFormSchema(): array
    {
        return [
            $this
                ->getTargetSelect()
                ->live()
                ->label('Feld')//ToDo Translate
                ->afterStateUpdated(fn($set) => $set('type', null)),
            ToggleButtons::make('type')
                ->options(fn() => [
                    'number' => static::__('number.label'),
                    'text' => static::__('text.label'),
                    'boolean' => static::__('bool.label'),
                    'null' => static::__('null.label'),
                    'option' => static::__('options.label'),
                ])
                ->afterStateUpdated($this->doAfterStateUpdate(...))
                ->disableOptionWhen($this->isOptionsDisabled(...))
                ->nullable(false)
                ->hiddenLabel()
                ->required()
                ->grouped()
                ->live(),
            $this->getTextTypeGroup()
                ->visible(fn($get) => $get('type') === 'text')
                ->live(),
            $this->getNumberTypeGroup()
                ->visible(fn($get) => $get('type') === 'number')
                ->live(),
            $this->getBooleanTypeGroup()
                ->visible(fn($get) => $get('type') === 'boolean')
                ->live(),
            $this->getOptionTypeGroup()
                ->visible(fn($get) => $get('type') === 'option')
                ->live(),
        ];
    }

    private function checkNull(mixed $targetValue): bool
    {
        if (is_null($targetValue)) {
            return true;
        }
        if (is_bool($targetValue) || $targetValue === '0') {
            return false;
        }

        return empty($targetValue);
    }

    private function isOptionsDisabled($value, Get $get, CustomForm $record): bool
    {
        if ($value !== 'option') {
            return false;
        }

        $target = $get('target');
        $formState = $get('../../../../../custom_fields') ?? [];

        foreach ($formState as $field) {
            $identifier = $field['identifier'] ?? '';

            if (!empty($field['general_field_id'])) {
                $customField = new TempCustomField($record, $field);
                $identifier = $customField->identifier();
            }

            if ($identifier === $target) {
                $customField = new TempCustomField($record, $field);

                return !($customField->getType() instanceof CustomOptionType);
            }
        }

        return true;
    }

    private function doAfterStateUpdate($get, $set, $old): void
    {
        if ($old === 'option') {
            $set('selected_options', []);
        }

        $type = $get('type');

        if ($type === 'text') {
            $set('values', []);
        } elseif ($type === 'option') {
            $set('selected_options', []);
        }
    }
}
