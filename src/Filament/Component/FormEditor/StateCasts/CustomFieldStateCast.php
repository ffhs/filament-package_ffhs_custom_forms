<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class CustomFieldStateCast implements StateCast
{

    public function flattCustomFields(array $customFields, int &$position = 1): array
    {
        $finalState = [];
        foreach ($customFields as $key => $customField) {
            $customField['form_position'] = $position;
            ++$position;
            if (array_key_exists('custom_fields', $customField)) {
                $finalState += $this->flattCustomFields($customField['custom_fields'] ?? [], $position);
                unset($customField['custom_fields']);
                $customField['layout_end_position'] = $position - 1;
            }
            $finalState[$key] = $customField;
        }

        return $finalState;
    }

    public function get(mixed $state): array
    {
        $state['custom_fields'] = $this->flattCustomFields($state['custom_fields']);
        return $state;
    }

    public function set(mixed $state): array
    {
        if (isset($state['custom_fields'])) {
            $state['custom_fields'] = $this->unflattenCustomFields($state['custom_fields']);
        }
        return $state;
    }

    public function unflattenCustomFields(array $flatCustomFields): array
    {
        $nested = [];
        $stack = [];

        // Sort by form_position to process in correct order
        uasort($flatCustomFields, function ($a, $b) {
            return ($a['form_position'] ?? 0) <=> ($b['form_position'] ?? 0);
        });

        foreach ($flatCustomFields as $key => $field) {
            // Clean up flattening artifacts
            $cleanField = $field;
            unset($cleanField['form_position']);

            // Check if this field has a layout_end_position (meaning it's a container)
            if (isset($field['layout_end_position'])) {
                $endPosition = $field['layout_end_position'];
                unset($cleanField['layout_end_position']);

                // Push this container onto the stack
                $stack[] = [
                    'key' => $key,
                    'field' => $cleanField,
                    'end_position' => $endPosition,
                    'children' => []
                ];
            } else {
                // This is a regular field
                if (!empty($stack)) {
                    // Add to the current container's children
                    $stack[count($stack) - 1]['children'][$key] = $cleanField;
                } else {
                    // Add directly to nested array
                    $nested[$key] = $cleanField;
                }
            }

            // Check if we've reached the end of any containers
            $currentPosition = $field['form_position'] ?? 0;
            while (!empty($stack) && $stack[count($stack) - 1]['end_position'] <= $currentPosition) {
                $container = array_pop($stack);
                $containerField = $container['field'];

                // Add children if any exist
                if (!empty($container['children'])) {
                    $containerField['custom_fields'] = $container['children'];
                }

                if (!empty($stack)) {
                    // Add to parent container
                    $stack[count($stack) - 1]['children'][$container['key']] = $containerField;
                } else {
                    // Add to root level
                    $nested[$container['key']] = $containerField;
                }
            }
        }

        // Handle any remaining containers in the stack
        while (!empty($stack)) {
            $container = array_pop($stack);
            $containerField = $container['field'];

            if (!empty($container['children'])) {
                $containerField['custom_fields'] = $container['children'];
            }

            if (!empty($stack)) {
                $stack[count($stack) - 1]['children'][$container['key']] = $containerField;
            } else {
                $nested[$container['key']] = $containerField;
            }
        }

        return $nested;
    }
}
