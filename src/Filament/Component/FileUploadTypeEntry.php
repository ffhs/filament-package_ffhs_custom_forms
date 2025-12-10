<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;

use Filament\Infolists\Components\Entry;

class FileUploadTypeEntry extends Entry
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.file-upload-display';

    protected function setUp(): void
    {
        parent::setUp();
        $this->hiddenLabel();
        $this->columns(1);
    }


}
