<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\Anchors;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules\FieldRuleAnchorType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;

class ValueEqualsRuleAnchor extends FieldRuleAnchorType
{

    public function shouldRuleExecute(CustomFormAnswer $formAnswer, CustomFieldAnswer $fieldAnswer, FieldRule $rule): bool {

    }

    public function createComponent(CustomForm $customForm, array $fieldData): Component {

    }
}
