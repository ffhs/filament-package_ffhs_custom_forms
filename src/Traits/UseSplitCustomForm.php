<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;


use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;

trait UseSplitCustomForm
{
    use HasLayoutSplit;
    use HasFieldSplit;
    use HasPosSplit;

    public function loadAnswerData(EmbedCustomFormAnswer $answer): array
    {
        if ($this->isUseLayoutTypeSplit()) {
            return $this->loadLayoutTypeSplitAnswerData($answer);
        }

        if ($this->isUseFieldSplit()) {
            return $this->loadFieldTypeSplitAnswerData($answer);
        }

        if ($this->isUsePoseSplit()) {
            return $this->loadPosTypeSplitAnswerData($answer);
        }

        return $this->loadCustomAnswerData($answer);
    }
}
