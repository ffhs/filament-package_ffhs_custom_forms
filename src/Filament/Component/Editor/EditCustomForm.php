<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\GroupType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\Types\SectionType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;

class EditCustomForm extends Field
{
    //use HasStateBindingModifiers;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom_field-edit';

    protected array $actionComponents = [];

    protected Closure|array $childComponents = [];


    protected function setUp(): void {
        $this->childComponents(function (): array {
            if(empty($this->actionComponents)) $this->generateChildActions();
            $actionComponents = collect($this->actionComponents)->flatten(2)->toArray();

            return [...$actionComponents];
        });
    }


   /* public function getState(): mixed {
        $state = parent::getState();

        dump($state);

       if(empty($state)){
           dd("ewtf");
            return [
                uniqid() => ['name' => "Test1" , "type" => TextType::identifier()],
                uniqid() => ['name' => "Test2", "type" => SectionType::identifier()],
                uniqid() => ['name' => "Test3", "type" => TextType::identifier()],
                uniqid() => [
                    'name' => "Test4",
                    "type" => GroupType::identifier(),
                    'custom_fields' => [
                        uniqid() => ['name' => "Test4-1", "type" => TextType::identifier()],
                        uniqid() => ['name' => "Test4-2", "type" => TextType::identifier()],
                    ],
                ],
                uniqid() => ['name' => "Test5", "type" => TextType::identifier()],
            ];
        }

        return $state;
    }*/



    public function getIconMap(): array {
        $array = [];
        foreach (CustomFieldType::getAllTypes() as $name => $type) {
            $array[$name] = $type::make()->icon();
        }
        return $array;
    }

    public function getTypeNameMap(): array {
        $array = [];
        foreach (CustomFieldType::getAllTypes() as $name => $type) {
            $array[$name] = $type::make()->getTranslatedName();
        }
        return $array;
    }


    public function getEditFieldActionContainer(string $key) {
        if(empty($this->actionComponents)) $this->generateChildActions();
        try {
            return ComponentContainer::make($this->getLivewire())
                ->components($this->actionComponents[$key])
                ->parentComponent($this);
        }catch (\ErrorException $e){
            dd($this->getState(), $e);
        }
    }


    public function generateChildActions(): void {
        $this->actionComponents =  [];

        $this->generateFieldActions($this->getState());
    }

    protected function generateFieldActions(array $fields): void {
        foreach ($fields as $key => $field){
            $type = CustomFieldUtils::getFieldTypeFromRawDate($field);

            if(is_null($type)) $actions = [];
            else $actions =  $type->getEditorActions($key, $field);

            $components = array_map(fn(Action $action) => $action->mergeArguments(["item"=> $key])->toFormComponent(), $actions);
            $this->actionComponents[$key] = $components;

            if(array_key_exists("custom_fields", $field)) $this->generateFieldActions($field["custom_fields"]);
        }
    }


    public function hasFieldComponent($state):bool{
        return CustomFieldUtils::getFieldTypeFromRawDate($state)?->fieldEditorExtraComponent($state) ?? false;
    }
    public function getFieldComponent($state):?string{
        return CustomFieldUtils::getFieldTypeFromRawDate($state)?->fieldEditorExtraComponent($state);
    }





}
