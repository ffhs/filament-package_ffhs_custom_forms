<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Collection;

class EditCustomFormFields extends Field
{
    //use HasStateBindingModifiers;

    /*
     * [
     *  structure = []
     *  data => []
     * ]
     */

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom_field-edit';


    protected array $actionContainers = [];
    protected array $nameContainers = [];

    protected bool $childrenGenerated = false;

    protected function setUp(): void {
        $this->childComponents(function (): array {
            $this->generateChildContainers();
            $containers = $this->getCombinedContainers();

            return $containers
                ->map(fn(ComponentContainer $container) => $container->getComponents())
                ->flatten(1)
                ->toArray();
        });
    }

    protected function getCombinedContainers(): Collection {
        return collect([
            ...array_values($this->getActionContainers()),
            ...array_values($this->getNameContainers()),
        ]);
    }

    public function getFieldDataState(): array {
        return $this->getState()["data"];
    }
    public function getStructureState(): array {
        return $this->getState()["structure"];
    }


    protected function generateChildContainers(): void {
        if($this->isChildrenGenerated()) return;


        $this->actionContainers = [];
        $this->nameContainers = [];

        foreach ($this->getFieldDataState() as $key => $field) {
            $type = CustomFieldUtils::getFieldTypeFromRawDate($field);

            $this->generateFieldActions($key, $field, $type);
            $this->generateNameContainers($key, $field, $type);
        }

        $this->childrenGenerated = true;
    }


    public function generateFieldActions(string $key, array $field, ?CustomFieldType $type): void {
        if (is_null($type)) $actions = [];
        else $actions = $type->getEditorActions($key, $field);

        $components = array_map(fn(Action $action) => $action->mergeArguments(["item" => $key]), $actions);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components([Actions::make($components)->columnSpanFull()->alignment(Alignment::Right)]);

        $this->actionContainers[$key] = $container;
    }



    public function generateNameContainers(string $key, array $field, ?CustomFieldType $type): void {
        $this->nameContainers[$key] = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath('data.'.$key)
            ->components([
                TextInput::make('name.'.app()->getLocale())
                    ->label("Name")
            ]);
    }



    public function getFieldActions(string $key): ComponentContainer {
        $this->generateChildContainers();
        return $this->getActionContainers()[$key];
    }

    public function getFieldName(string $key): ComponentContainer {
        $this->generateChildContainers();
        return $this->getNameContainers()[$key];
    }



    public function getFieldComponent(string $key):?string{
        return $this->getFieldType($key)?->fieldEditorExtraComponent($this->getFieldDataState()[$key]);
    }


    public function getFieldType(string $key): ?CustomFieldType {
        return CustomFieldUtils::getFieldTypeFromRawDate($this->getFieldDataState()[$key]);
    }


    public function getActionContainers(): array {
        return $this->actionContainers;
    }

    public function getNameContainers(): array {
        return $this->nameContainers;
    }

    protected function isChildrenGenerated(): bool {
        return $this->childrenGenerated;
    }

}
