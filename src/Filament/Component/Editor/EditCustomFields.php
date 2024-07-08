<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\DragDropComponent;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

class EditCustomFields extends DragDropComponent
{

    protected function setUp(): void {

        $this->label("");

        $this->flatten();
        $this->dragDropGroup("custom_fields");

        $this->gridSize(4); //ToDo Make Config
        $this->nestedFlattenListType(CustomField::class);

        $this->itemGridSize(function ($itemState){
            if(empty($itemState['options'])) return null;
            return $itemState['options']['column_span'] ?? null;
        });
        $this->itemGridStart(function ($itemState){
            if(empty($itemState['options'])) return null;
            return $itemState['options']['column_start'] ?? null;
        });


        $this->flattenGrid(function ($itemState){
            if(empty($itemState['options'])) return null;
            return $itemState['options']['columns'] ?? null;
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
