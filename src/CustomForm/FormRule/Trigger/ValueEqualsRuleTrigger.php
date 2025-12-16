<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Trigger;

use Ffhs\FfhsUtils\Contracts\Rules\EmbedRuleTrigger;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts\CustomFieldStateCast;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasBoolCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasNumberCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleTriggerPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTextCheck;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeOptionFormEditorComponent;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;

class ValueEqualsRuleTrigger extends FormRuleTriggerType
{
    use HasTypeOptionFormEditorComponent;
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
            return $component->live(); //ToDo improve with js functions but holy moly
        }

        return $component;
    }

    public function isTrigger(array $arguments, mixed &$target, EmbedRuleTrigger $trigger): bool
    {
        if (!array_key_exists('state', $arguments)) {
            return false;
        }

        if (empty($trigger->data)
            || empty($trigger->data['target'])
            || empty($trigger->data['type'])) {
            return false;
        }

        $targetFieldIdentifier = $trigger->data['target'];

        $state = $arguments['state'];

        if ($state instanceof CustomFormAnswer) {
            $fieldAnswer = $state->customFieldAnswers->firstWhere('customField.identifier', $targetFieldIdentifier);
            /**@var CustomFieldAnswer $fieldAnswer */
            $targetValue = $fieldAnswer?->customField->getType()->prepareLoadAnswerData($fieldAnswer,
                $fieldAnswer->answer);
        } else {
            $targetValue = [];
        }


        $type = $trigger->data['type'];

        return match ($type) {
            'number' => $this->checkNumber($targetValue, $trigger->data),
            'text' => $this->checkText($targetValue, $trigger->data),
            'boolean' => $this->checkBoolean($targetValue, $trigger->data),
            'null' => $this->checkNull($targetValue),
            'option' => $this->checkOption($targetValue, $trigger->data),
            default => false,
        };
    }

    public function getConfigurationSchema(): array
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
        $customFormIdentifier = $get('../../../../../custom_form_identifier') ?? [];
        $formConfiguration = CustomForms::getFormConfiguration($customFormIdentifier);

        $flattenFields = new CustomFieldStateCast()->flattCustomFields($formState);
        $fields = $this->getFieldDataFromFormData($flattenFields, $formConfiguration);

        foreach ($fields as $field) {
            /**@var EmbedCustomField $field */

            $identifier = $field->identifier();

            if ($identifier === $target) {
                return !($field->getType() instanceof CustomOptionType);
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
