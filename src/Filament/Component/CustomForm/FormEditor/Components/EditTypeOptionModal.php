<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Group;

class EditTypeOptionModal extends Group
{

   // protected string $view = 'filament-forms::components.group';

    protected function setUp(): void {
        parent::setUp();


        $this
            ->schema(function($state, CustomForm $record){
                $field = new CustomField();
                $field->fill($state);

                $disabledOption = $field->overwritten_options;

                $components = $field->getType()->getExtraTypeOptionComponents();

                foreach ($components as $item) {
                    if ($item instanceof Field) $item->disabled(in_array($item->getStatePath(false),$disabledOption));

                    elseif ($item instanceof \Filament\Forms\Components\Section) {
                        foreach ($item->getChildComponents() as $field) {
                            $field->disabled(in_array($field->getStatePath(false),$disabledOption));
                        }
                    }
                }


                return[
                    Group::make($components)
                        ->statePath("options")
                        ->columns()
                ];
        });
    }



}
