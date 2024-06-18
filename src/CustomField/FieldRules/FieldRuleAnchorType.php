<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRules;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Domain\Type;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FieldRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Infolists\Components\Component as InfoComponent;
use Illuminate\Support\Collection;

abstract class FieldRuleAnchorType extends Type
{

    /*
     * Static Class Functions
     */
    /*  public static function getAllAnchors():array{
          $output = [];
          foreach(config("ffhs_custom_forms.field_rule_anchor_types") as $typeClass)
              $output[$typeClass::identifier()]= $typeClass;
          return $output;
      }
      public static function getTypeClassFromIdentifier(string $typeName): ?string {
          $types = self::getAllAnchors();
          if(!array_key_exists($typeName,$types)) return null;
          return self::getAllAnchors()[$typeName];
      }
    public static function getTypeFromIdentifier(string $typeName): ?FieldRuleAnchorType {
        $class = self::getTypeClassFromIdentifier($typeName);
        if(is_null($class)) return null;
        return new $class();
    }*/


    public static function getConfigTypeList():string {
        return "field_rule_anchor_types";
    }

    public abstract function settingsComponent(CustomForm $customForm, array $fieldData):Component;
    public abstract function getCreateAnchorData():array; //ToDo I think it is possible to replace something in the Formmodal of the action to load the default values
    public abstract function shouldRuleExecute(array $formState, Component|InfoComponent $component, FieldRule $rule):bool;

    public function canRuleExecute(Component|InfoComponent $component, FieldRule $rule ):bool {
        if($component instanceof Component) $rawFormData = array_values($component->getLivewire()->getCachedForms())[0]->getRawState();
        else {
            /** @var InfoComponent $component*/
            $allComponents = collect($component->getInfolist()->getFlatComponents(true));
            $rawFormData = $allComponents->map(function (InfoComponent $value) {
                return [ "key" => $value->getKey(), "state" => $value->getState()];
            })->pluck("state","key")->toArray();
        }
        return $this->shouldRuleExecute($rawFormData, $component ,$rule);
    }


    public function getTranslatedName(): string {
        return __("custom_forms.anchors" . self::identifier());
    }

    public function getDisplayName(array $ruleData, Repeater $component, Get $get): string {
        return $this->getTranslatedName();
    }

    public function afterAllFormComponentsRendered(FieldRule $rule, Collection $components):void {

    }

    public function canAddOnField(CustomFieldType $type): bool {
        return true;
    }
    public function mutateRenderParameter(array $parameter, FieldRule $rule): array {
        return $parameter;
    }
    public function mutateDataBeforeLoadInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }

    public function mutateDataBeforeSaveInEdit(array $ruleData, FieldRule $rule): array {
        return $ruleData;
    }

    public function mutateOnTemplateDissolve(array $data, FieldRule $originalRule, CustomField $originalField):array {
        unset($data["id"]);
        return $data;
    }
}
