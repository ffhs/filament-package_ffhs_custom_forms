<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\CustomFormEditor;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

trait HasTypeOptionFormEditorComponent
{

    protected function getFormEditorComponent(
        string $schemaComponentPath,
        Page|RelationManager $livewire
    ): ?CustomFormEditor {
        $pathSet = explode('.', $schemaComponentPath);
        $formName = $pathSet[0];

        array_shift($pathSet);

        /** @var Schema $form */
        $form = $livewire->$formName;

        for ($path = 0, $pathMax = count($pathSet); $path < $pathMax; $path++) {
            $component = $form->getComponentByStatePath(implode('.', $pathSet));
            if ($component instanceof CustomFormEditor) {
                return $component;
            }
            array_pop($pathSet);
        }

        return null;
    }

}
