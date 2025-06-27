<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanRenderCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasStaticMake;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Group as FormsGroup;
use Filament\Infolists\Components\Component as InfolistsComponent;
use Filament\Infolists\Components\Group as InfolistsGroup;

class TemplateTypeView implements FieldTypeView
{
    use HasStaticMake;
    use CanRenderCustomForm;

    public function getFormComponent(
        TemplateFieldType|CustomFieldType $type,
        CustomField $record,
        array $parameter = []
    ): FormsComponent {
        $schema = $this->renderTemplate($record, $parameter);

        return FormsGroup::make($schema)
            ->columns(config('ffhs_custom_forms.default_column_count'))
            ->columnSpanFull();
    }

    public function getInfolistComponent(
        TemplateFieldType|CustomFieldType $type,
        CustomFieldAnswer $record,
        array $parameter = []
    ): InfolistsComponent {
        $schema = $this->renderTemplate($record, $parameter);

        return InfolistsGroup::make($schema)
            ->columnSpanFull();
    }

    protected function renderTemplate(CustomField|CustomFieldAnswer $customField, array $parameter)
    {
        if ($customField instanceof CustomFieldAnswer) {
            $customField = $customField->customField;
        }

        //Setup Render Data
        $fields = $customField->template->customFields;
        $viewMode = $parameter['viewMode'];
        $form = $customField->customForm;
        $displayer = $parameter['displayer'];

        //Render Schema Input
        $renderedOutput = $this->renderCustomFormRaw(
            $viewMode,
            $displayer,
            $form,
            $fields,
            0
        );

        //Register Components for rule stuff
        $allComponents = $renderedOutput[1];
        $parameter['registerComponents']($allComponents);

        //return Schema
        return $renderedOutput[0];
    }
}
