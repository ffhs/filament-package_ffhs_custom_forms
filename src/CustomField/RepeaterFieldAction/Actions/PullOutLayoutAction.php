<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\Actions;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\RepeaterFieldAction\RepeaterFieldAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;

class PullOutLayoutAction extends RepeaterFieldAction
{

    //CustomFieldAnswerer CustomField id changing is handelt in TemplateFieldType.class on afterEditFieldDelete()
    public function getAction(CustomForm $record, array $typeClosers): Action {
        return Action::make("pullOut")
            ->visible(fn(array $state, array $arguments)=> $this->isVisible($record,$typeClosers,$state,$arguments))
            ->icon('heroicon-m-arrow-long-left')
            ->action(function (array $arguments, array $state, $set, Get $get) {
                $itemIndex = $arguments["item"];
                $newUpperState = $get("../../custom_fields");

                $newUpperState[$itemIndex] = $state[$itemIndex];
                $set("../../custom_fields", $newUpperState);

                $newState = $get("custom_fields");
                unset($newState[$itemIndex]);
                $set("custom_fields", $newState);
            })
            ->hidden(function ($arguments, $state, $get) {
                return is_null($get("../../custom_fields"));
            });
    }

}
