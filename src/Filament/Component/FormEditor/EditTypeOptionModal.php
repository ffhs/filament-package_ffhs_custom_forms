<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;

class EditTypeOptionModal extends Component
{
    protected string $view = 'filament-schemas::components.grid';


    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema($this->getTypeOptionSchema(...));
    }

    protected function getTypeOptionSchema(): array
    {
        return once(function () {
            $state = $this->getState();
            $field = new CustomField();
            $field->fill($state);

            $disabledOption = $field->overwritten_options;
            $components = $field
                ->getType()
                ->getExtraTypeOptionComponents();

            foreach ($components as $item) { //ToDo test
                /** @var Component $item */
                $items = ($item instanceof Field) ? [$item] : $item->getDefaultChildComponents();

                foreach ($items as $itemField) {
                    $itemKey = $itemField->getStatePath(false);
                    $isItemDisabled = in_array($itemKey, $disabledOption, false);
                    $itemField->disabled($isItemDisabled);
                }
            }

            return [
                Group::make($components)
                    ->statePath('options')
                    ->columns()
            ];
        });
    }
}
