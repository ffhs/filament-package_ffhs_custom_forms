<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\TemplatesType;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;

class TemplateTypeView implements FieldTypeView
{

    public static function getFormComponent(TemplateFieldType|CustomFieldType $type, CustomField $record, array $parameter = []): Component {

        $schema = static::renderTemplate($record, $parameter);

        return Group::make($schema)
            ->columns(config("ffhs_custom_forms.default_column_count"))
            ->columnSpanFull();
    }

    protected static function renderTemplate(CustomField|CustomFieldAnswer $customField, array $parameter)
    {
        if($customField instanceof CustomFieldAnswer) $customField = $customField->customField;

        //Setup Render Data
        $fields = $customField->template->customFields;
        $viewMode = $parameter['viewMode'];
        $form = $customField->customForm;
        $render = $parameter["render"];

        //Render Schema Input
        $renderedOutput = CustomFormRender::renderRaw(
            0,
            $fields,
            $render,
            $viewMode,
            $form
        );

        //Register Components for rule stuff
        $allComponents = $renderedOutput[1];
        $parameter["registerComponents"]($allComponents);

        //return Schema
        return $renderedOutput[0];
    }

    public static function getInfolistComponent(TemplateFieldType|CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {

        $schema = static::renderTemplate($record, $parameter);

        return \Filament\Infolists\Components\Group::make($schema)
            ->columnSpanFull();
    }


}
