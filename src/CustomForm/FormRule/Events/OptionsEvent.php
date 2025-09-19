<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\Events;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\CustomOptionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\TempCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanMapFields;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasRuleEventPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTriggerEventFormTargets;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Traits\EnumeratesValues;

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
                ->hidden(function ($set, $get) {
                    //Fields with an array doesn't generate properly
                    if (is_null($get('selected_options'))) {
                        $set('selected_options', []);
                    }
                })
                ->options($this->getCustomOptionsOptions(...))
        ];
    }

    public function getCustomOptionsOptions(
        $get,
        CustomForm $record
    ): \Illuminate\Database\Eloquent\Collection|EnumeratesValues|Enumerable|array|Collection {
        $field = $this->getTargetFieldData($get, $record);

        if (empty($field)) {
            return [];
        }

        if (!empty($field['general_field_id'])) {
            $customField = new TempCustomField($record, $field);
            $genOptions = $customField
                ->getGeneralField()
                ->customOptions;

            $selectedOptions = $this->getTargetFieldData($get, $record)['options']['customOptions'] ?? [];
            $genOptions = $genOptions->whereIn('id', $selectedOptions);

            return $genOptions->pluck('name', 'identifier');
        }

        if (!array_key_exists('options', $field)) {
            $field['options'] = [];
        }
        if (!array_key_exists('customOptions', $field['options'])) {
            $field['options']['customOptions'] = [];
        }

        $options = $field['options']['customOptions'];

        return collect($options)
            ->pluck('name.' . $record->getLocale(), 'identifier');
    }


    protected function getTargetOptions($get, $record): array
    {
        //ToDo fix
        $output = [];
        collect($this->getAllFieldsData($get, $record))
            ->map(function ($field) use ($record) {
                $customField = new CustomField($field);

                if ($customField->isGeneralField()) {
                    $genField = $record
                        ->getFormConfiguration()
                        ->getAvailableGeneralFields()
                        ->get($customField->general_field_id);
                    $customField->setRelation('generalField', $genField);
                }

                if ($customField->custom_form_id === $record->id) {
                    $customField->setRelation('customForm', $record);
                } else {
                    $template = $record
                        ->getFormConfiguration()
                        ->getAvailableTemplates()
                        ->get($customField->custom_form_id);
                    $customField->setRelation('customForm', $template);
                }

                return $customField;
            })
            ->filter(fn(CustomField $field) => $field->getType() instanceof CustomOptionType)
            ->each(function (CustomField $field) use ($record, &$output) {
                $title = $field->customForm?->short_title;

                if (empty($title)) {
                    $title = '?';
                }

                $output[$title][$field->identifier] = $field->name ?? ' ';
            });

        return $output;
    }
}
