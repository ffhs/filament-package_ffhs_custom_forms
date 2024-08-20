<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormRule\old\Anchors;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAnchorAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\HasAnchorPluginTranslate;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Component as InfoComponent;

class IsInfolistViewRuleAnchor extends FieldRuleAnchorAbstractType
{
    use HasAnchorPluginTranslate;

    public static function identifier(): string {
        return "infolist_view";
    }

    public function settingsComponent(CustomForm $customForm, array $fieldData): Component {
        return Group::make();
    }

    public function getCreateAnchorData(): array {
        return [];
    }

    public function shouldRuleExecute(array $formState, Component|InfoComponent $component, FieldRule $rule): bool {
        return $component instanceof InfoComponent;
    }
}
