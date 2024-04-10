<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;

class TemplateTypeView implements FieldTypeView
{

    public static function getFormComponent(TemplateFieldType|CustomFieldType $type, CustomField $record, array $parameter = []): Component {

        return Group::make(CustomFormRender::generateFormSchema($record->template, "default"))
            ->columnSpanFull();
    }


    public static function getInfolistComponent(TemplateFieldType|CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {
        $viewMode = $parameter["viewMode"];
        $formAnswer = $record->customFormAnswer;
        $form = $record->customField->template;
        $customFields = $form->customFields;

        $fieldAnswers = $formAnswer->cachedAnswers()->whereIn("custom_field_id", $customFields->select("id")->flatten());

        $render= CustomFormRender::getInfolistRender($viewMode,$form,$formAnswer, $fieldAnswers);
        $customViewSchema = CustomFormRender::render(0,$customFields,$render,$viewMode)[0];
        return \Filament\Infolists\Components\Group::make($customViewSchema)
            ->columnSpanFull();
        //->columns(config("ffhs_custom_forms.default_column_count"));
    }
}
