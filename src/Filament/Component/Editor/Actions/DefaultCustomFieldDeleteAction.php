<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionContainer;

class DefaultCustomFieldDeleteAction extends Action
{
    protected function setUp(): void {

       parent::setUp();

       $this->iconButton();
       $this->icon('heroicon-c-trash');
       $this->color('danger');

       $this->closeModalByClickingAway(false);

        //ToDo Confirm Message
       $this->requiresConfirmation();

       $this->action(function($get, $set, $state, $arguments, ActionContainer $component) {
           $key = $arguments["item"];

           //Delete Structure
           //ToDo move to function
           $path =  explode('.', $component->getStatePath());
           $path = '../' . $path[count($path)-1];
           $state = $get($path);

           $toDelete = $state[$key];

           //Delete Fields
           unset($state[$key]);

           //Delete Sub Fields
           $amountDeletedFields = 1;
           if(!empty($toDelete['layout_end_position'])){
               foreach ($state as $keyField => $field){
                   if($field['layout_end_position'] >= $toDelete['form_position']  && $toDelete['form_position'] < $field['form_position']){
                       unset($state[$keyField]);
                       $amountDeletedFields++;
                   }
               }
           }


           //Rearrange Fields
           foreach ($state as $keyField => $field){
               if($toDelete['form_position'] < $field['form_position'])
                   $state[$keyField]['form_position'] = $field['form_position'] - $amountDeletedFields;
               if($toDelete['form_position'] < $field['layout_end_position'])
                   $state[$keyField]['layout_end_position'] = $field['layout_end_position'] - $amountDeletedFields;
           }

           $set($path,$state); //ToDo show if it work
       });
    }

    function getPath(array $structure, string $key): ?string {
        foreach ($structure as $item => $value) {
            if ($item == $key) {
                return $item;
            } else if (!empty($value)) {
                $pathSegment = $this->getPath($value,$key);
                if(is_null($pathSegment)) continue;
                return  $item . "." . $pathSegment;
            }
        }
        return null;
    }


    function getSubFields(array $structure): array {
        $fields = [];
        foreach ($structure as $item => $value) {
            $fields[] = $item;

            if (!empty($value)) {
                $subFields = $this->getSubFields($value);
                $fields = array_merge($subFields, $fields);
            }
        }
        return $fields;
    }
}
