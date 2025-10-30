<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Barryvdh\Debugbar\Facades\Debugbar;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\CustomFormEditor;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasAllFieldDataFromFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFieldsMapToSelectOptions;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
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

    /**
     * @param Get $get
     * @param Page<InteractsWithForms, HasForms>|RelationManager $livewire
     * @return array|Collection
     */
    protected function getOptions(Get $get, Page|RelationManager $livewire): array|Collection
    {
        $actionPath = $get('../../context.schemaComponent');
        $pathSet = explode('.', $actionPath);
        $formName = $pathSet[0];
        array_shift($pathSet);
        /** @var Schema $form */
        $form = $livewire->$formName;

        /** @var CustomFormEditor|null $customFormComponent */
        $customFormComponent = null;
        for ($path = 0, $pathMax = count($pathSet); $path < $pathMax; $path++) {
            $component = $form->getComponentByStatePath(implode('.', $pathSet));
            Debugbar::info($component::class);
            if ($component instanceof CustomFormEditor) {
                $customFormComponent = $component;
                break;
            }
            array_pop($pathSet);
        }

        if (is_null($customFormComponent)) {
            throw new \RuntimeException('CustomFormEditor not found');
        }

        $state = $customFormComponent->getState()['custom_fields'] ?? [];
        $formConfiguration = $customFormComponent->getFormConfiguration();

        $fields = collect($this->getFieldDataFromFormData($state, $formConfiguration));

        return $this->getSelectOptionsFromFields($fields, $formConfiguration);
    }
}
