<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\EmbeddedCustomForm;

trait UseSplitCustomForm
{
    use HasLayoutSplit;
    use HasFieldSplit;
    use HasPosSplit;

    public function loadAnswerData(EmbeddedCustomForm $component): array
    {
        $record = $component->getCustomFormAnswer();

        if (is_null($record)) {
            return [];
        }

        if ($component->isUseLayoutTypeSplit()) {
            return $component->loadLayoutTypeSplitAnswerData($record);
        }

        if ($component->isUseFieldSplit()) {
            return $component->loadFieldTypeSplitAnswerData($record);
        }

        if ($component->isUsePoseSplit()) {
            return $component->loadPosTypeSplitAnswerData($record);
        }

        return $component->loadCustomAnswerData($record);
    }
}
