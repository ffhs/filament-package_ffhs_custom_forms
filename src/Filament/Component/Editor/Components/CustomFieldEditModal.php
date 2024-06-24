<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;

class CustomFieldEditModal extends Section
{
    use UseComponentInjection;

    protected string $view = 'filament-forms::components.group';

   /* public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFieldUtils::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions =  $type->getExtraTypeOptions() > 1;
        if (!$hasOptions) return 'xl';
        return '5xl';
    }*/


    protected function setUp(): void {
        parent::setUp();

      /* $fieldData = $this->getState();

        $isGeneral = array_key_exists("general_field_id",$fieldData)&& !empty($fieldData["general_field_id"]);
        $type = CustomFieldUtils::getFieldTypeFromRawDate($fieldData);
       // $columns = $isGeneral?1:2;*/

        $this
            ->columnSpanFull()
           // ->columns($columns)
            ->schema([

              //  FieldModalOptionSection::make($type)->columnSpan(1),

              Group::make()
                  ->statePath("options")
                  ->schema(function(){
                      $type = CustomFieldUtils::getFieldTypeFromRawDate($this->getState());
                      return $type->getExtraTypeOptionComponents();
                  })
                  ->columns()


              // FieldModalRuleSection::make([$this->form,$type])->columnSpanFull()
        ]);
    }




}
