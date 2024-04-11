<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Support\Htmlable;

class FieldModalOptionSection extends Section
{
    use UseComponentInjection;

    public static function make(string | array | Htmlable | Closure | null| CustomFieldType $heading = null): static {
        return self::injectIt($heading, ['heading' => "Optionen"]); //ToDo Translate
    }


    protected function setUp(): void {
        $type = $this->injection;
        parent::setUp();

        $this->schema([
            Fieldset::make()
                ->schema([
                    Toggle::make('is_active')
                        ->visible($type->canBeDeactivate())
                        ->label("Aktive"), //ToDo Translate

                    // Required
                    Toggle::make('required')
                        ->visible($type->canBeRequired())
                        ->label("Benötigt"), //ToDo Translate

                ]),
            Fieldset::make()
                ->statePath("options")
                ->visible($type->hasExtraTypeOptions())
                ->schema($type->getExtraTypeOptionComponents())
        ]);
    }

}
