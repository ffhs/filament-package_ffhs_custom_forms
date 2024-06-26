<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
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
        $this->registerActions([
            Action::make("createField")
                ->action(static::createField(...)),
        ]);


        $this->childComponents(function (): array {
            $this->generateChildContainers();
            $containers = $this->getCombinedContainers();

            return $containers
                ->map(fn(ComponentContainer $container) => $container->getComponents())
                ->flatten(1)
                ->toArray();
        });
    }

    protected function createField($set, $arguments, $get): void {
        //Set Field Data
        //Make custom modes

        $mode = $arguments["mode"];

        $managers = config("ffhs_custom_forms.editor.field_creator_managers"); //EditCreateFieldManager
        if(!array_key_exists($mode, $managers)) return;

        $key = uniqid();
        $fieldData = $managers[$mode]::getFieldData($this, $get($this->getStatePath(false)) ,$arguments, $key);


        $set($this->getStatePath(false). $key, $fieldData);


        $this->setStructureNewField($arguments, $get, $key, $set);
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

        if($type->hasEditorNameElement($field))
            $elements = [
                TextInput::make('name.'.app()->getLocale())
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
        $fields = collect($this->getState());
        $fields = $fields->map(fn(array $fieldData) => (new CustomField())->fill($fieldData));

        return  $this->loadStructure($fields);
    }


    private function loadStructure(Collection $fields): array {
        if($fields->count() === 0) return [];

        $fields = $fields->sortBy('form_position');
        $start = array_values($fields->toArray())[0]['form_position'];
        $end = array_values($fields->toArray())[$fields->count()-1]['form_position'];


        $structure = [];

        for ($i = $start; $i <= $end; $i++) {
            /**@var CustomField $field */
            $field = $fields->firstWhere('form_position', $i);

            if(empty($field->layout_end_position)) {
                $structure[$field->identifier] = [];
                continue;
            }

            $subFields = $field->customForm->getOwnedFields()
                ->where("form_position", ">", $field->form_position)
                ->where("form_position", "<=", $field->layout_end_position);


            $i = $field->layout_end_position;
            $structure[$field->identifier] = static::loadStructure($subFields);
        }

        return  $structure;
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



    private function setStructureNewField($arguments, $get, string $uuid, $set): void {
        //SetStructure
        $path = $this->getStatePath(false).'.structure.'.$arguments["path"];
        $structureFragment = $get($path);

        $before = $arguments['before'];

        if ($arguments['before'] == "") $structureFragment[$uuid] = [];
        else {
            $pos = array_search($before, array_keys($structureFragment));

            $structureFragment = array_slice($structureFragment, 0, $pos, true) +
                [$uuid => []] +
                array_slice($structureFragment, $pos, count($structureFragment), true);

        }

        $set($path, $structureFragment);
    }

}
