<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFieldEditModal;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormEditForm\EditCustomFieldRule;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Editor\CustomFormEditorHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CustomFieldEditModal extends Component
{

    protected CustomForm $form;
    protected array $fielData;

    public static function getEditCustomFormActionModalWith(array $state): string {
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($state);
        if (!empty($state["general_field_id"])) return 'xl';
        $hasOptions = $type->canBeRequired() || $type->canBeDeactivate() || $type->hasExtraTypeOptions();
        if (!$hasOptions) return 'xl';
        return '5xl';
    }


    public static function getCustomFieldSchema(array $data, CustomForm $customForm):array{
        //ToDo change and import it hier
        return EditCustomFieldForm::getCustomFieldSchema($data,$customForm);
    }



    public static function make(CustomForm $form,array $fieldData): static {
        $static = app(static::class, ['form' => $form, 'fieldData'=>$fieldData]);
        $static->configure();
        return $static;
    }

    public function __construct(CustomForm $form, array $fielData) {
        $this->form = $form;
        $this->fielData = $fielData;
    }


    protected function setUp(): void {
        parent::setUp();
        $fieldData = $this->fielData;

        $isGeneral = array_key_exists("general_field_id",$fieldData)&& !empty($fieldData["general_field_id"]);
        $type = CustomFormEditorHelper::getFieldTypeFromRawDate($fieldData);
        $columns = $isGeneral?1:2;

        $this->schema([
            Group::make()
                ->columns($columns)
                ->columnSpanFull()
                ->label("")
                ->schema([
                    Tabs::make()
                        ->columnStart(1)
                        ->hidden($isGeneral)
                        ->tabs([
                            $this->getTranslationTab("de","Deutsch"),
                            $this->getTranslationTab("en","Englisch"),
                        ]),

                    EditCustomFieldForm::getFieldOptionSection($type)
                        ->columnSpan(1),

                    EditCustomFieldRule::getRuleComponent($customForm,$type)

                ]),
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

    private function getFieldOptionSection(CustomFieldType $type): Section {
        return Section::make("Optionen") //ToDo Translate
        ->schema([
            Fieldset::make()
                ->schema([
                    Toggle::make('is_active')
                        ->visible($type->canBeDeactivate())
                        ->label("Aktive"), //ToDo Translate

                    // Required
                    Toggle::make('required')
                        ->visible($type->canBeRequired())
                        ->label("Benötigt"), //ToDo Translate

                ]),
            Fieldset::make()
                ->statePath("options")
                ->visible($type->hasExtraTypeOptions())
                ->schema($type->getExtraTypeOptionComponents())

        ]);
    }

}
