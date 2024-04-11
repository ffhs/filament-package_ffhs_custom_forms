<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal\Rule\FieldModalRuleSection;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\Helper\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\UseComponentInjection;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;

class CustomFieldEditModal extends Component
{
    use UseComponentInjection;

    protected string $view = 'filament-forms::components.group';

    protected array $fieldData;
    protected CustomForm $form;

    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired() || $type->canBeDeactivate() || $type->hasExtraTypeOptions();
        if (!$hasOptions) return 'xl';
        return '5xl';
    }


    public static function make(CustomForm $form, array $fieldData): static {
        $static = app(static::class, ['form' => $form, 'fieldData'=>$fieldData]);
        $static->configure();
        return $static;
    }

    public function __construct(CustomForm $form, array $fieldData) {
        $this->form = $form;
        $this->fieldData = $fieldData;
    }


    protected function setUp(): void {
        parent::setUp();
        $fieldData = $this->fieldData;

        $isGeneral = array_key_exists("general_field_id",$fieldData)&& !empty($fieldData["general_field_id"]);
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($fieldData);
        $columns = $isGeneral?1:2;

        $this
            ->columnSpanFull()
            ->columns($columns)
            ->schema([
                Tabs::make()
                    ->columnStart(1)
                    ->hidden($isGeneral)
                    ->tabs([
                        $this->getTranslationTab("de","Deutsch"),
                        $this->getTranslationTab("en","Englisch"),
                    ]),

                FieldModalOptionSection::make($type)->columnSpan(1),

                FieldModalRuleSection::make([$this->form,$type])->columnSpanFull()
        ]);
    }


    private function getTranslationTab(string $location, string $label): Tab {
        return Tab::make($label)
            ->schema([
                TextInput::make("name_" . $location)
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.name'))
                    ->required(),
                TextInput::make("tool_tip_" . $location)
                    ->label(__('filament-package_ffhs_custom_forms::custom_forms.fields.tool_tip')),
            ]);
    }



}
