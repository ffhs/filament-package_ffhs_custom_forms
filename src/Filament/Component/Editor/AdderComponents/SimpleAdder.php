<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;

abstract class SimpleAdder extends FormEditorFieldAdder
{

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.field-adder.simple_adder';


    abstract function getTitle(): string;


    // value => label
    abstract function getFieldsToAdd(): array;

    abstract function getAddMode(): string;

    public function isOptionDisables($id):bool {
        return false;
    }

    abstract function getDisabledColor(): string;

    abstract function getAdderId():string;

    abstract function getBorderColor(): string;
    abstract function getHoverColor(): string;

    protected function setUp(): void {
        parent::setUp();
        $this->live();
        $this->label($this->getTitle());
    }




}
