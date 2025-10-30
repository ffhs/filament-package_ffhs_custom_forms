<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer;


use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\EntryFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEmbeddedCustomForm;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Schema;
use Filament\Support\Components\Component;
use Illuminate\Support\HtmlString;

/**
 * @method null|CustomFormAnswer getCachedExistingRecord()
 */
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

    /**
     * @return array<Component|string|HtmlString>
     */
    public function getDefaultChildComponents(): array
    {
        return once(function (): array {
            return $this->getCustomFormSchema($this->getCustomForm(), $this->getFieldDisplayer(), $this->getViewMode());
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fieldDisplayer(EntryFieldDisplayer::make(...))
            ->columns(1)
            ->autoViewMode()
            ->hiddenLabel();
    }

    protected function makeChildSchema(string $key): Schema
    {
        return parent::makeChildSchema($key)->operation('view');
    }
}
