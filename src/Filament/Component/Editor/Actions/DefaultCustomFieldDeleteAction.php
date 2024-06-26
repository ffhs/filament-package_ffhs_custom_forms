<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Actions;

use Filament\Forms\Components\Actions\Action;

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

       $this->action(function($get, $arguments, $set, $state) {
           $key = $arguments["item"];

           //Delete Structure
           //ToDo repair
           $structure = $get("structure");
           $structurePath = "structure." . $this->getPath($structure, $key);

           $structurePath = str_replace(".". $key,"", $structurePath);
           $structureFragment = $get($structurePath);


           unset($state[$key]);


           foreach ($this->getSubFields($structureFragment[$key]) as $field){
               unset($state[$field]);
           }

           $set(".",$state); //ToDo show if it work

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
