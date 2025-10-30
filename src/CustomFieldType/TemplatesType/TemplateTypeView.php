<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType;

use Ffhs\FfhsUtils\Traits\HasStaticMake;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFormDataContainer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadCustomFormEditorData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Components\Component;

class TemplateTypeView implements FieldTypeView
{
    use HasStaticMake;
    use CanRenderCustomForm;
    use CanLoadCustomFormEditorData;

    public function getFormComponent(EmbedCustomField $customField, array $parameter = []): Component
    {
        $schema = $this->renderTemplate($customField, $parameter);

        return Group::make($schema)
            ->columns(config('ffhs_custom_forms.default_column_count'))
            ->columnSpanFull();
    }

    public function getEntryComponent(EmbedCustomFieldAnswer $customFieldAnswer, array $parameter = []): Component
    {
        $schema = $this->renderTemplate($customFieldAnswer->getCustomField(), $parameter);


        return Section::make()
            ->contained(false)
            ->schema($schema)
            ->columnSpanFull();
    }

    protected function renderTemplate(EmbedCustomField $customField, array $parameter)
    {
        if ($customField instanceof CustomFieldAnswer) {
            $customField = $customField->customField;
        }

        //Setup Render Data
//        $fields = $customField->template->customFields;
        $viewMode = $parameter['viewMode'];
        $form = $customField->getTemplate();
        /**@phpstan-ignore-next-line */ //ToDo Fix for EmbeddedCustomField
        $form = CustomFormDataContainer::make($this->loadCustomFormEditorData($form));
        $displayer = $parameter['displayer'];

        //Render Schema Input
        $renderedOutput = $this->renderCustomFormRaw(
            $viewMode,
            $displayer,
            $form,
            $form->getOwnedFields(),
            0
        );

        //Register Components for rule stuff
        $allComponents = $renderedOutput[1];
        $parameter['registerComponents']($allComponents);


        //return Schema
        return $renderedOutput[0];
    }
}
