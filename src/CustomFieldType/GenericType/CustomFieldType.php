<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType;

use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAnswerCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFieldTypeConfig;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEditFieldCallbacks;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEditorLayoutElements;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldSplitting;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasGridModifiers;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeView;

abstract class CustomFieldType implements Type
{
    use IsType;
    use HasTypeView;
    use HasTypeOptions;
    use HasCustomFieldTypeConfig;
    use HasEditFieldCallbacks;
    use HasGridModifiers;
    use HasFieldSplitting;
    use HasEditorLayoutElements;
    use HasAnswerCallbacks;

    /** @var array<string, class-string<FieldTypeView>> $viewModes */
    protected static array $viewModes;
    protected static string $identifier;
    protected static string $icon;

    public static function identifier(): string
    {
        return static::$identifier;
    }

    final public static function getTypeListConfig(): array
    {
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

        $allTypes = array_merge($types, $genFields, $extraTypes, $formConfigTypes);

        return array_unique($allTypes);
    }

    final public static function getSelectableGeneralFieldTypes(): array
    {
        $output = [];

        foreach (config('ffhs_custom_forms.selectable_general_field_types') as $typeClass) {
            $output[$typeClass::identifier()] = $typeClass;
        }

        return $output;
    }

    public static function displayname(): string
    {
        return __('custom_forms.types.' . static::identifier());
    }

    public function icon(): string
    {
        return static::$icon;
    }

    /**
     * @return array<string, class-string<FieldTypeView>>
     */
    public function viewModes(): array
    {
        return static::$viewModes;
    }

    public function canBeDeactivate(): bool
    {
        return true;
    }
}
