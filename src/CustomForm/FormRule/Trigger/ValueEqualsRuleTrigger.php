<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasBoolCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasNumberCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTextCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;

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

    public function prepareComponent(
        Component|\Filament\Infolists\Components\Component $component,
        RuleTrigger $trigger
    ): Component|\Filament\Infolists\Components\Component {
        if ($component instanceof Component) {
            return $component->live();
        }

        return $component;
    }

    public function isTrigger(array $arguments, mixed &$target, RuleTrigger $rule): bool
    {
        if (!array_key_exists('state', $arguments)) {
            return false;
        }

        if (empty($rule->data)) {
            return false;
        }
        if (empty($rule->data['target'])) {
            return false;
        }
        if (empty($rule->data['type'])) {
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
            $this->getTargetSelect()
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
        if (is_bool($targetValue)) {
            return false;
        }
        if ($targetValue === '0') {
            return false;
        }
        return empty($targetValue);
    }

    private function isOptionsDisabled($value, Get $get, CustomForm $record): bool
    {
        if ($value !== 'option') {
            return false;
        }
        //ToDo may better way
        return once(function () use ($get, $record) {
            $target = $get('target');
            $formState = $get('../../../../../custom_fields') ?? [];
            $customField = [];
            foreach ($formState as $field) {
                $customField = new CustomField($field);
                $customField = $this->loadFieldRelationsFromForm($customField, $record);

                if ($customField->identifier() === $target) {
                    break;
                }

                $customField = null;
            }

            if (empty($customField)) {
                return true;
            }
            return !($customField->getType() instanceof CustomOptionType);
        });
    }

    private function doAfterStateUpdate($get, $set, $old): void
    {
        if ($old === 'option') {
            $set('selected_options', []);
        }

        switch ($get('type')) {
            case'text':
                $set('values', []);
                break;
            case'option':
                $set('selected_options', []);
                break;
        }
    }
}
