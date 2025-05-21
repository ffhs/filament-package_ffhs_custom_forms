<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAllFieldDataFromFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldsMapToSelectOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

class RelatedFieldOption extends TypeOption
{
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;
    use HasOptionNoComponentModification;

    public function getDefaultValue(): mixed
    {
        return null;
    }

    public function getComponent(string $name): Component
    {
        return Select::make($name)
            ->label(TypeOption::__('related_field.label'))
            ->helperText(TypeOption::__('related_field.helper_text'))
            ->options($this->getOptions(...));
    }

    protected function getOptions($livewire): array|Collection
    {
        $get = $livewire->getMountedFormComponentActionComponent(0)->getGetCallback();
        $fields = collect($this->getFieldDataFromFormData($get('../custom_fields')))
            ->map(fn(array $field) => (new CustomField())->fill($field));

        return $this->getSelectOptionsFromFields($fields);
    }
}
