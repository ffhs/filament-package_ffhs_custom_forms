<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAllFieldDataFromFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldsMapToSelectOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;

class RelatedFieldOption extends TypeOption
{
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;
    use HasOptionNoComponentModification;

    public function getComponent(string $name): Component
    {
        return Select::make($name)
            ->label(TypeOption::__('related_field.label'))
            ->helperText(TypeOption::__('related_field.helper_text'))
            ->options($this->getOptions(...));
    }

    protected function getOptions(Get $get): array|Collection
    {
        $fields = collect($this->getFieldDataFromFormData($get('../custom_fields'),
            CustomForms::getFormConfiguration($get('../custom_form_identifier'))))
            ->map(fn(array $field) => app(CustomField::class)->fill($field));

        return $this->getSelectOptionsFromFields($fields, $get);
    }
}
