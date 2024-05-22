<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;

abstract class FormEditorFieldAdder extends Component {

    protected string $view = 'filament-forms::components.group';
    protected CustomForm $form;

    public static function make(CustomForm $form): static {
        $static = app(static::class, [
            'form' => $form,
        ]);
        $static->configure();

        return $static;
    }

    public final function __construct(CustomForm $form) {
        $this->form = $form;
    }

    protected function setUp(): void {
        parent::setUp();

        $this->schema(array_merge([
            Placeholder::make("")
                ->label($this->getTitle())
                ->content("")
                ->columnSpanFull()
        ], $this->getSchema()));
    }

    protected function addCustomFieldInRepeater(array $data, Get $get, $set): void {
        CustomFormEditorHelper::setCustomFieldInRepeater($data, $get, $set);
    }

    abstract function getSchema(): array;

    abstract function getTitle(): string;

}
