<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Collection;

class EditCustomFormFields extends Field
{
    use HasStateBindingModifiers;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom_field-edit';
    protected array $actionContainers = [];
    protected array $nameContainers = [];

    protected bool $childrenGenerated = false;

    protected function setUp(): void {

        //CreateField Actions
        $fieldCreateActions = [];
        foreach (config("ffhs_custom_forms.editor.field_creator_action")  as $mode => $actionClass)
            $fieldCreateActions[] = $actionClass::make($mode . "-create_field");
        $this->registerActions($fieldCreateActions);

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


    protected function generateChildContainers(): void {
        if($this->isChildrenGenerated()) return;

        $this->actionContainers = [];
        $this->nameContainers = [];

        foreach ($this->getState() as $key => $field) {
            $this->generateFieldActions($key, $field);
            $this->generateNameContainers($key, $field);
        }

        $this->childrenGenerated = true;
    }


    public function generateFieldActions(string $key, array $field): void {
        $type = CustomFieldUtils::getFieldTypeFromRawDate($field);

        if (is_null($type)) $actions = [];
        else $actions = $type->getEditorActions($key, $field);

        $components = array_map(fn(Action $action) => $action->mergeArguments(["item" => $key]), $actions);

        $container = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components([Actions::make($components)->columnSpanFull()->alignment(Alignment::Right)]);

        $this->actionContainers[$key] = $container;
    }



    public function generateNameContainers(string $key, array $field): void {
        $type = CustomFieldUtils::getFieldTypeFromRawDate($field);
        $elements = [];


        if( is_null($type)) return;

        /**@var CustomForm $record*/
        $record = $this->getRecord();

        if($type->hasEditorNameElement($field))
            $elements = [
                TextInput::make('name.'. $record->getLocale())
                    ->hidden(fn($state)=> false)
                    ->label(''),
            ];

        $this->nameContainers[$key] = ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->statePath($key)
            ->components($elements);
    }


    public function getFieldActions(string $key): ComponentContainer {
        $this->generateChildContainers();
        $actions = $this->getActionContainers();

        if(array_key_exists($key, $actions)) return $actions[$key];
        $this->generateFieldActions($key, $this->getState()[$key]);
        return $this->getActionContainers()[$key];
    }

    public function getFieldName(string $key): ComponentContainer {
        $this->generateChildContainers();

        $names = $this->getNameContainers();

        if(array_key_exists($key, $names)) return $names[$key];

        $this->generateNameContainers($key, $this->getState()[$key]);

        return $this->getNameContainers()[$key];
    }

    public function getFieldComponent(string $key):?string{
        return $this->getFieldType($key)?->fieldEditorExtraComponent($this->getState()[$key]);
    }


    public function getGeneralField($key): ?GeneralField {
        $data = $this->getState()[$key];
        $genId = $data['general_field_id'] ?? null;
        if(is_null($genId)) return null;
        return GeneralField::cached($genId);
    }


    public function getStructure(): array {
        $list = NestedFlattenList::make($this->getState(), CustomField::class);
        return $list->getStructure(true);
    }

    public function getFieldType(string $key): ?CustomFieldType {

        return CustomFieldUtils::getFieldTypeFromRawDate($this->getState()[$key]);
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
