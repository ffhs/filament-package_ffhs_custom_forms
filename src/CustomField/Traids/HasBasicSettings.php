<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

trait HasBasicSettings
{

    public function getExtraOptionFields(?GeneralField $generalField = null): array {
        return [
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,
        ];
    }

    public function getExtraOptionSchema(?GeneralField $generalField = null): ?array {
        return [
            $this->getColumnSpanOption(),
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption()
        ];
    }


    protected function getColumnSpanOption() {
        return TextInput::make("column_span")
            ->label("Zeilenweite")//ToDo Translation
            ->step(1)
            ->integer()
            ->minValue(1)
            ->maxValue(10)
            ->required();
    }
    protected function getInLineLabelOption() {
        return Toggle::make("in_line_label")
            ->label("Title in der Zeile");//ToDo Translation
    }
    protected function getNewLineOption() {
        return Toggle::make("new_line_option")
            ->label("Neue Zeile");//ToDo Translation
    }

}
