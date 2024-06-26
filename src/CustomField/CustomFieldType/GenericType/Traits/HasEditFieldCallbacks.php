<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait HasEditFieldCallbacks
{


    private function mutateOptions(array &$data, CustomField $field, string $method): void {
        $options = $data['options'];

        foreach ($this->getFlattenExtraTypeOptions() as $key => $option){
            $options[$key] = $option->$method($options[$key], $key, $field);
        }
        $data['options'] = $options;
    }

    private function doForOptions(array &$data, CustomField $field, string $method): void {
        $options = $data['options'];
        foreach ($this->getFlattenExtraTypeOptions() as $key => $option){
            $option->$method($options[$key], $key, $field);
        }
        $data['options'] = $options;
    }


    public final function getMutateCustomFieldDataOnSave(CustomField $field, array $data): array {
        $this->mutateOptions($data, $field, 'mutateOnFieldSave');
        //ToDo For Rules
        return $this->mutateCustomFieldDataOnSave($field, $data);
    }

    public final function getMutateCustomFieldDataOnLoad(CustomField $field, array $data): array {
        $this->mutateOptions($data, $field, 'mutateOnFieldLoad');
        //ToDo For Rules
        return $this->mutateCustomFieldDataOnLoad($field, $data);
    }

    public function doBeforeSaveField(CustomField $field, array &$data): void {
        $this->doForOptions($data, $field, 'beforeSaveField');
        //ToDo For Rules
        $this->beforeSaveField($field, $data);
    }

    public function doAfterSaveField(CustomField $field, array $data): void {
        $this->doForOptions($data, $field, 'afterSaveField');
        //ToDo For Rules
        $this->afterSaveField($field, $data);
    }

    public function doAfterCreateField(CustomField $field, array $data): void {
        $this->doForOptions($data, $field, 'afterCreateField');
        //ToDo For Rules
        $this->afterCreateField($field, $data);
    }

    public function doBeforeDeleteField(CustomField $field): void {
        foreach ($this->getFlattenExtraTypeOptions() as $key => $option){
            /**@var TypeOption $option*/
            $option->beforeDeleteField($key, $field);
        }

        $this->beforeDeleteField($field);
    }

    public function doAfterDeleteField(CustomField $field): void {
        foreach ($this->getFlattenExtraTypeOptions() as $key => $option){
            /**@var TypeOption $option*/
            $option->afterDeleteField($key, $field);
        }
        $this->afterDeleteField($field);
    }




    public final function mutateCustomFieldDataOnLoad(CustomField $field, array $data): array {return $data;}
    public function mutateCustomFieldDataOnSave(CustomField $field, array $data): array {return $data;}
    public function beforeSaveField(CustomField $field, array $data): void {}
    public function afterSaveField(CustomField $field, array $data): void {}
    public function afterCreateField(CustomField $field, array $data): void {$this->doAfterSaveField($field, $data);}
    public function beforeDeleteField(CustomField $field): void {}
    public function afterDeleteField(CustomField $field): void {}



}
