<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\AdderComponents;


use Filament\Forms\Components\Component;

abstract class FormEditorFieldAdder extends Component {

    protected array $listeners = ['createField'];

    protected string $view = 'filament-forms::components.group';

    public static function make(): static {
        $static = app(static::class);
        $static->configure();

        return $static;
    }


    protected function createField($path,$after, $mode, $value): void {
        dd($path,$after, $mode, $value);
    }

    protected function setUp(): void {
     /*   parent::setUp();


        $this->schema(array_merge([
            Placeholder::make("")
                ->label($this->getTitle())
                ->content("")
                ->columnSpanFull()
        ], $this->getSchema()));*/
    }

    protected function addCustomFieldData(array $data, $key): void {
        //ToDo
    }

    //abstract function getSchema(): array;

    //abstract function getTitle(): string;

}
