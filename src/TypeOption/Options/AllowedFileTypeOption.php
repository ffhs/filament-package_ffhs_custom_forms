<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\TypeOption\Options;

use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasOptionNoComponentModification;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\TagsInput;
use Filament\Support\Components\Component;

class AllowedFileTypeOption extends TypeOption
{
    use HasOptionNoComponentModification;

    protected mixed $default = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
    ];

    public function getComponent(string $name): Component
    {
        return TagsInput::make($name)
            ->columnSpanFull()
            ->label(TypeOption::__('allowed_file_types.label'))
            ->helperText(TypeOption::__('allowed_file_types.helper_text'));
    }
}
