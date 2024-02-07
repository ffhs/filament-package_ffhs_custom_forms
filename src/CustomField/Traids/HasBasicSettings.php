<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Traids;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

trait HasBasicSettings
{

    public function getExtraOptionFieldsTrait(): array {
        return [];
    }
    public function getExtraOptionFields(): array {
        return array_merge($this->getExtraOptionFieldsTrait(),[
            'column_span' => 3,
            'in_line_label' => false,
            'new_line_option' => true,
        ]);
    }

    public function getExtraOptionSchema(): ?array {
        return [
            $this->getColumnSpanOption(),
            $this->getNewLineOption()->columnStart(1),
            $this->getInLineLabelOption()
        ];
    }


    protected function getColumnSpanOption() :TextInput {
        return TextInput::make("column_span")
            ->label("Zeilenweite")//ToDo Translation
            ->step(1)
            ->integer()
            ->minValue(1)
            ->maxValue(10)
            ->required();
    }
    protected function getInLineLabelOption() :Toggle{
        return Toggle::make("in_line_label")
            ->label("Title in der Zeile");//ToDo Translation
    }
    protected function getNewLineOption() :Toggle{
        return Toggle::make("new_line_option")
            ->label("Neue Zeile");//ToDo Translation
    }

}
