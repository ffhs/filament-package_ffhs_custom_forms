<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\FlattedNested\NestedFlattenList;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

class EditCustomFields extends DragDropComponent
{

    private function getGridColumns($itemState): ?int
    {
        $columns = $itemState['options']['columns'] ?? null;
        if (!empty($columns)) return $columns;
        $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
        return $type->getStaticColumns();
    }

    protected function setUp(): void {

        $this->label("");

        $this->flatten();
        $this->dragDropGroup("custom_fields");

        $this->gridSize(4); //ToDo Make Config
        $this->nestedFlattenListType(CustomField::class);

        $this->itemGridSize(function ($itemState, $state, EditCustomFields $component){
               $size = $itemState['options']['column_span'] ?? null;
               if(!empty($size)) return $size;
               $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
               if(!$type->isFullSizeField())return null;
               $maxSize = 10;
               $position = $itemState["form_position"];
               $nerastParent = 0;
               foreach ($state as $item){
                   if($item["form_position"] >= $position) continue;
                   if($item["layout_end_position"]?? 0 < $position) continue;
                   if($position-$nerastParent < $position-$item["form_position"]) continue;
                   $nerastParent = $item["form_position"];
                   $maxSize = $this->getGridColumns($item);
               }

               return $maxSize;
        });


        $this->itemGridStart(function ($itemState){
           if(empty($itemState['options'])) return null;
           $newLineOption = $itemState['options']['new_line_option'] ?? false;
           return $newLineOption ? 1: null;
       });

      $this->flattenGrid(function ($itemState){
          return $this->getGridColumns($itemState);
      });

       $this->flattenViewHidden(function ($itemState){
           $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
           return is_null($type) || is_null($type->fieldEditorExtraComponent($itemState));
       });

        $this->flattenView(function ($itemState){
            $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
            return $type->fieldEditorExtraComponent($itemState);
        });


        $this->itemLabel(function ($itemState){
            $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
            if(is_null($type)) return null;
            return $type->getEditorFieldTitle($itemState);
        });
        $this->itemIcons(function ($itemState){
            $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
            if(is_null($type)) return null;
            return $type->getEditorFieldIcon($itemState);
        });
        $this->flattenViewLabel("Felder"); //ToDo Translate


        $this->itemActions(function ($itemState, $item){
            $type = CustomFieldUtils::getFieldTypeFromRawDate($itemState);
            if(is_null($type)) return [];
            return $type->getEditorActions($item, $itemState); //i think it doesnt need the key
        });



        $this->schema([
            Group::make()
                ->schema(fn($record, $state)=> [
                    TextInput::make('name.'. $record->getLocale())
                        ->label("")
                        ->visible(function () use ($state) {
                            $type = CustomFieldUtils::getFieldTypeFromRawDate($state);
                            return !is_null($type) && $type->hasEditorNameElement($state);
                        })
                ])
        ]);


    }

}
