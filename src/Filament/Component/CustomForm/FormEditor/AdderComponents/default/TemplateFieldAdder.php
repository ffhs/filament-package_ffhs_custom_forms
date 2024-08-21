<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\default;

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\FormEditor\AdderComponents\FormEditorFieldAdder;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\DragDrop\Actions\DragDropExpandActions;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\EditHelper\EditCustomFormHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Cache;

final class TemplateFieldAdder extends FormEditorFieldAdder
{

    protected function setUp(): void
    {
        $this->hidden(fn($state) => $state['is_template']);
        $this->label( __("filament-package_ffhs_custom_forms::custom_forms.navigation.templates"));
        $this->schema([
            DragDropExpandActions::make()
                ->dragDropGroup("custom_fields")
                ->options($this->getGeneralFieldSelectOptions(...))
                ->disableOptionWhen($this->isTemplateDisabled(...))
                ->color(Color::Green)
                ->action(fn($option)=>
                 //   Action::make("addTemplate")->action(fn($component,$arguments) => $this->createField($arguments, $component, $option))
                    AddTemplateFieldAction::make("addTemplate")->option($option)
                )
        ]);
    }


    public function getGeneralFieldSelectOptions(): array
    {
        return CustomForm::getTemplateTypesToAdd($this->getRecord()->getFormConfiguration())
            ->pluck("short_title", "id")
            ->toArray();

    }
    public function useTemplateUsedGeneralFields(int $templateId): bool {
        $templateGenIds = CustomForm::cached($templateId)->generalFields->pluck("id")->toArray();
        $existingIds = EditCustomFormHelper::getUsedGeneralFieldIds($this->getState()['custom_fields']);
        $commonValues = array_intersect($templateGenIds, $existingIds);

        return !empty($commonValues);
    }

    public function isTemplateDisabled($value):bool {
        if($this->useTemplateUsedGeneralFields($value)) return true;

        $usedTemplateIds =   Cache::remember($this->getState()['id'] . '_general_fields_not_allowed_in_form', GeneralField::getCacheDuration()/4.0, function (){
            $templates =array_filter($this->getState()['custom_fields'], fn($da) => !empty($da['template_id']));
            return array_map(fn($template) => $template["template_id"],$templates);
        });
        return in_array($value,$usedTemplateIds);


    }

}
