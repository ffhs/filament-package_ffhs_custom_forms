<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOption;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\TypeOption\TypeOptionGroup;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;

trait HasEditFieldCallbacks
{

    public function mutateCustomFieldDataOnSave(array $data): array {return $data;}

    public function mutateCustomFieldDataOnLoad(CustomField $field, array $data): array {return $data;}


    public function beforeSaveField(CustomField|\Closure|null $field, array $data): void {}
    public function afterSaveField(CustomField $field, array $data): void {}
    public function afterCreateField(CustomField $field, array $data): void {
        $this->afterSaveField($field, $data);
    }

    public function beforeDeleteField(CustomField $field): void {}

    public function afterDeleteField(CustomField $field): void {}

}
