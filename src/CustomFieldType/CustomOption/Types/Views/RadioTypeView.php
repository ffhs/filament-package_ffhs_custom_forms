<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\Types\Views;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\CustomOption\HasCustomOptionInfoListViewWithBoolean;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasDefaultViewComponent;
use Filament\Forms\Components\Radio;
use Filament\Support\Components\Component;

class RadioTypeView implements FieldTypeView
{
    use HasCustomOptionInfoListViewWithBoolean;
    use HasDefaultViewComponent;


    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        /**@var Radio $radio */
        $radio = $this->makeComponent(Radio::class, $customField, false);
        $radio = $radio->inline($this->getOptionParameter($customField, 'inline'));

        if ($this->getOptionParameter($customField, 'boolean')) {
            $radio->boolean();
        } else {
            $radio->options($this->getAvailableCustomOptions($customField));
        }

        return $radio;
    }
}
