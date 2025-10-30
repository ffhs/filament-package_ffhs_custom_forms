<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAllFieldDataFromFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldsMapToSelectOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasTypeOptionFormEditorComponent;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Components\Component;

class RelatedFieldOption extends TypeOption
{
    use HasAllFieldDataFromFormData;
    use HasFieldsMapToSelectOptions;
    use HasOptionNoComponentModification;
    use HasTypeOptionFormEditorComponent;

    public function getComponent(string $name): Component
    {
        return Select::make($name)
            ->label(TypeOption::__('related_field.label'))
            ->helperText(TypeOption::__('related_field.helper_text'))
            ->options($this->getOptions(...));
    }

    /**
     * @param Get $get
     * @param Page|RelationManager $livewire
     * @return array<string|int, array<string, string>>
     */
    protected function getOptions(Get $get, Page|RelationManager $livewire): array
    {
        $customFormComponent = $this->getFormEditorComponent($get('../../context.schemaComponent'), $livewire);

        if (is_null($customFormComponent)) {
            throw new \RuntimeException('CustomFormEditor not found');
        }

        $state = $customFormComponent->getState()['custom_fields'] ?? [];
        $formConfiguration = $customFormComponent->getFormConfiguration();

        $fields = collect($this->getFieldDataFromFormData($state, $formConfiguration));

        /**@phpstan-ignore-next-line */
        return $this->getSelectOptionsFromFields($fields, $formConfiguration);
    }
}
