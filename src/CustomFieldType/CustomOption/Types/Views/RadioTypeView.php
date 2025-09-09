<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Support\Components\Component;
use Filament\Forms\Components\Radio;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;

    public function getFormComponent(
        CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): Component {
        /**@var Radio $radio */
        $radio = $this->makeComponent(Radio::class, $record);
        $radio = $radio->inline($this->getOptionParameter($record, 'inline'));

        if ($this->getOptionParameter($record, 'boolean')) {
            $radio->boolean();
        } else {
            $radio->options($this->getAvailableCustomOptions($record));
        }

        return $radio;
    }
}
