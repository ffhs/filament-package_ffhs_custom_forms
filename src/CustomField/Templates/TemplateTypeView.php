<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomField\Templates;

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomOption\HasCustomOptionInfoListView;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\FormMapper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\View\FieldTypeView;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;

class TemplateTypeView implements FieldTypeView
{

    public static function getFormComponent(CustomFieldType $type, CustomField $record, array $parameter = []): Component {

        return Group::make(CustomFormRender::generateFormSchema($record->template, "default"))
            ->columnSpanFull();
    }


    public static function getInfolistComponent(CustomFieldType $type, CustomFieldAnswer $record, array $parameter = []): \Filament\Infolists\Components\Component {
        //$renderAnswers $record->customFormAnswer->customFieldAnswers->whereIn("custom_field_id", $record->customField->template->customFields->select("id"));
        return \Filament\Infolists\Components\Group::make() //CustomFormRender::gener($record->c, "default") ToDo make infolist view
            ->columnSpanFull();
    }
}
