<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer;


use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEmbeddedCustomForm;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;

class CustomFormAnswerEntry extends Entry implements CanEntangleWithSingularRelationships
{
    use HasEmbeddedCustomForm;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-answer-entry';

    public function fillFromRelationship(): void
    {
        $data = $this->loadCustomAnswerForEntry($this->getCachedExistingRecord());
        $data = $this->mutateRelationshipDataBeforeFill($data);

        $this->getChildSchema()?->fill($data, false, false);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this
            ->fieldDisplayer(EntryFieldDisplayer::make(...))
            ->schema($this->getCustomFormSchema(...))
            ->columns(1)
            ->autoViewMode()
            ->hiddenLabel();
    }

}
