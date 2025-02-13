<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAllFieldDataFromFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldsMapToSelectOptions;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

class RelatedFieldOption extends TypeOption
{
    use TypeOptionPluginTranslate;
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;

    public function getDefaultValue(): mixed {
        return null;
    }

    protected  function getOptions($livewire): array|Collection{
        $get = $livewire->getMountedFormComponentActionComponent(0)->getGetCallback();
        $fields = collect($this->getFieldDataFromFormData($get('../custom_fields')))
            ->map(fn(array $field) => (new CustomField())->fill($field));

        return $this->getSelectOptionsFromFields($fields);
    }

    public function getComponent(string $name): Component
    {
       return Select::make($name)
           ->label($this->translate('related_field'))
           ->options($this->getOptions(...));
    }
}
