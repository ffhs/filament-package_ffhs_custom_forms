<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormEditor\FieldAdder;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanModifyCustomFormEditorData;
use Filament\Forms\Components\Concerns\CanGenerateUuids;
use Filament\Support\Colors\Color;
use Filament\Support\Components\Component;

class FormGeneralFieldAdder extends FormFieldAdder
{
    use CanModifyCustomFormEditorData;
    use CanGenerateUuids;

    public static function getSiteComponent(CustomFormConfiguration $configuration): Component
    {
        return self::make('add_template_node')
            ->formConfiguration($configuration);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(3)
            ->label(CustomForm::__('pages.general_field_adder.label'))
            ->disableOptionWhen($this->isGeneralDisabled(...))
            ->options($this->getGeneralFieldSelectOptions(...))
            ->action($this->onAdd(...))
            ->color(Color::Blue)
            ->expandSelect();
    }

    protected function onAdd(array $arguments): void
    {
        $this->addNewField([
            'general_field_id' => $arguments['option'],
            'is_active' => true,
        ]);
    }

    protected function getGeneralFieldSelectOptions(): array
    {
        return once(function () {
            $formConfiguration = $this->getFormConfiguration();

            return $formConfiguration
                ->getAvailableGeneralFields()
                ->mapWithKeys(function (GeneralField $generalField) use ($formConfiguration) {
                    //Mark Required GeneralFields
                    /**@var GeneralFieldForm $generalFieldForm */
                    $generalFieldForm = $generalField
                        ->generalFieldForms
                        ->firstWhere('custom_form_identifier', $formConfiguration::identifier());
                    $name = ($generalFieldForm->is_required ? '* ' : '') . ($generalField->name ?? '404');

                    return [$generalField->id => $name];
                })->toArray();
        });
    }

    protected function isGeneralDisabled($value, $get): bool
    {
        $notAllowed = once(function () use ($get) {
            $fields = $this->getCustomFieldsState();
            $usedGeneralFieldIds = collect($fields)
                ->whereNotNull('general_field_id')
                ->pluck('general_field_id');

            if (!is_null($get('template_identifier'))) {
                return $usedGeneralFieldIds;
            }

            $usedTemplates = collect($fields)
                ->pluck('template_id');
            $usedTemplates = $this->getFormConfiguration()
                ->getAvailableTemplates()
                ->whereIn('id', $usedTemplates);
            $usedGeneralFieldIdsFormTemplates = $usedTemplates
                ->map(fn(CustomForm $template) => $template->ownedFields)
                ->flatten(1)
                ->whereNotNull('general_field_id')
                ->pluck('general_field_id');

            return $usedGeneralFieldIds
                ->merge($usedGeneralFieldIdsFormTemplates)
                ->map(fn($id) => (int)$id)
                ->toArray();
        });

        return in_array($value, $notAllowed, true);
    }
}
