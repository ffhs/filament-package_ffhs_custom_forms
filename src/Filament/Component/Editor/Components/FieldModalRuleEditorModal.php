<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\Editor\Components;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldUtils;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAnchorAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FieldRulesOld\FieldRuleAbstractType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

class FieldModalRuleEditorModal extends Component
{
    protected string $view = 'filament-forms::components.group';

    protected CustomFieldType $type;


    public static function make(CustomFieldType $type): static {
        $static = app(static::class, ['type'=>$type]);
        $static->configure();
        return $static;
    }


    public function __construct(CustomFieldType $type) {
        $this->type = $type;
    }

    protected function setUp(): void {
        parent::setUp();

        $this->columns();
        $this->schema(fn()=>[
            $this->getAnchorEditSection(),
            $this->getRuleEditSection()
        ]);
    }

    protected function getAnchorEditSection(): Section {
        $anchors = $this->getSelectableAnchors($this->getRecord(), $this->type);
        return Section::make("Abhängigkeit")//ToDo Translate
        ->columnSpan(1)
            ->schema([
                Select::make("anchor_identifier")
                    ->disabled(fn($get)=> !is_null($get("anchor_identifier")))
                    ->label("Art") //ToDo Translate
                    ->options($anchors)
                    ->required()
                    ->afterStateUpdated(function ($state,$set){
                        if(is_null($state)) return;
                        $set("anchor_data", FieldRuleAnchorAbstractType::getTypeFromIdentifier($state)?->getCreateAnchorData());
                    })
                    ->live(),
                Group::make()
                    ->statePath("anchor_data")
                    ->columnStart(1)
                    ->columnSpanFull()
                    ->columns()
                    ->schema(function($get,  $livewire) {
                        if(is_null($get("anchor_identifier"))) return [];
                        $data = $livewire->data;

                        $data = CustomFieldUtils::flattDownToCustomFields($data);

                        $anchor = FieldRuleAnchorAbstractType::getTypeFromIdentifier($get("anchor_identifier"));
                        return [$anchor->settingsComponent($this->getRecord(), $data["custom_fields"])];
                    }),
            ]);
    }

    protected function getSelectableAnchors(CustomForm $customForm, CustomFieldType $type): array {
        $allAnchors = $type->overwrittenAnchorRules();
        if(is_null($allAnchors)) $allAnchors = $customForm->getFormConfiguration()::anchorRuleTypes();
        $anchors = [];
        foreach ($allAnchors as $anchorClass) {
            /**@var FieldRuleAnchorAbstractType $anchor */
            $anchor = new $anchorClass();
            if (!$anchor->canAddOnField($type)) continue;
            $anchors[$anchor->identifier()] = $anchor->getTranslatedName();
        }
        return $anchors;
    }


    protected function getRuleEditSection(): Section {


        $rules = $this->getSelectableRules($this->type);
        return Section::make("Feldaktion")
            ->columnSpan(1)
            ->schema([
                Select::make("rule_identifier")
                    ->disabled(fn($get)=> !is_null($get("rule_identifier")))
                    ->label("Aktion") //ToDo Translate
                    ->options($rules)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set){
                        if(is_null($state)) return;
                        $set("rule_data", FieldRuleAbstractType::getTypeFromIdentifier($state)?->getCreateRuleData());
                    }),
                Group::make()
                    ->statePath("rule_data")
                    ->schema(function($get, $livewire) {
                        if(is_null($get("rule_identifier"))) return [];
                        $data = $livewire->data;

                        $data = CustomFieldUtils::flattDownToCustomFields($data);
                        $rule = FieldRuleAbstractType::getTypeFromIdentifier($get("rule_identifier"));
                        return [$rule->settingsComponent($this->getRecord(), $data["custom_fields"])];
                    }),
            ]);
    }


    protected function getSelectableRules(CustomFieldType $type): array {
        $customForm = $this->getRecord();
        $allRules = $type->overwrittenRules();
        if(is_null($allRules)) $allRules = $customForm->getFormConfiguration()::ruleTypes();
        $rules = [];
        foreach ($allRules as $ruleClass) {
            /**@var FieldRuleAbstractType $rule */
            $rule = new $ruleClass();
            if (!$rule->canAddOnField($type)) continue;
            $rules[$rule->identifier()] = $rule->getTranslatedName();
        }
        return $rules;
    }


}
