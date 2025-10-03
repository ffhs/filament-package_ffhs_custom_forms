<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

abstract class OptionsEvent extends FormRuleEventType
{
    use HasRuleEventPluginTranslate;
    use HasTriggerEventFormTargets;
    use CanMapFields;

    public function getConfigurationSchema(): array
    {
        return [
            $this->getTargetSelect(),
            Select::make('selected_options')
                ->label('Anzuzeigende Optionen')
                ->multiple()
                ->options($this->getCustomOptionsOptions(...))
                ->hidden(function ($set, $get) {
                    is_null($get('selected_options')) ? $set('selected_options', []) : null;
                })
        ];
    }

    public function getCustomOptionsOptions(
        Get $get,
        Model $record
    ): \Illuminate\Database\Eloquent\Collection|Enumerable|array|Collection {
        $field = $this->getTargetFieldData($get);

        if (is_null($field)) {
            return [];
        }

        if ($field->isGeneralField()) {
            $genOptions = $field->getGeneralField()->customOptions;
            
            $selectedOptions = $field->customOptions->pluck('id') ?? [];
            $genOptions = $genOptions->whereIn('id', $selectedOptions);

            return $genOptions->pluck('name', 'identifier');
        }

        $options = $field->options ?? [];
        $local = method_exists($record, 'getLocale') ? $record->getLocale() : app()->getLocale();

        if (array_key_exists('customOptions', $options)) {
            $options = $options['customOptions'];
        } else {
            $options = [];
        }

        return collect($options)->pluck('name.' . $local, 'identifier');
    }

    protected function getTargetOptions($get): array
    {
        $formConfiguration = $this->getFormConfiguration($get);
        $fields = collect($this->getAllFieldsData($get, $formConfiguration))
            ->filter(fn(EmbedCustomField $field) => is_null($field->template_id))
            ->filter(fn(EmbedCustomField $field) => $field->getType() instanceof CustomOptionType);

        return $this->getSelectOptionsFromFields($fields, $get);
    }
}
