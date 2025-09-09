<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAnswerCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasConfigAttribute;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEditFieldCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEditorLayoutElements;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldSplitting;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasGridModifiers;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\IsType;

abstract class CustomFieldType implements Type
{
    use IsType;
    use HasTypeView;
    use HasTypeOptions;
    use HasConfigAttribute;
    use HasEditFieldCallbacks;
    use HasGridModifiers;
    use HasFieldSplitting;
    use HasEditorLayoutElements;
    use HasAnswerCallbacks;

    final public static function getTypeListConfig(): array
    {
        $path = static::getConfigTypeList();
        $formConfig = CustomForms::config('form_configurations', []);
        $types = CustomForms::config('default_form_configuration.selectable_field_types', []);
        $genFields = CustomForms::config('selectable_general_field_types', []);
        $extraTypes = CustomForms::config('extra_custom_field_types', []);

        $formConfigTypes = collect($formConfig)
            ->map(function ($item) {
                return $item['selectable_field_types'] ?? null;
            })
            ->filter(fn($item) => !is_null($item))
            ->flatten(1)
            ->toArray();

        $allTypes = array_merge($types + $genFields + $extraTypes + $formConfigTypes);

        return array_unique($allTypes);
    }

    final public static function getConfigTypeList(): string
    {
        return 'custom_field_types';
    }

    final public static function getSelectableGeneralFieldTypes(): array
    {
        $output = [];

        foreach (config('ffhs_custom_forms.selectable_general_field_types') as $typeClass) {
            $output[$typeClass::identifier()] = $typeClass;
        }

        return $output;
    }

    final public static function getSelectableFieldTypes(): array
    {
        $output = [];

        foreach (config('ffhs_custom_forms.selectable_field_types') as $typeClass) {
            $output[$typeClass::identifier()] = $typeClass;
        }

        return $output;
    }

    abstract public function viewModes(): array;

    public function getTranslatedName(): string
    {
        return __('custom_forms.types.' . $this::identifier());
    }

    public function canBeDeactivate(): bool
    {
        return true;
    }
}
