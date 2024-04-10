<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render;

use App\Domain\CustomForm\CustomLayoutType\AProductFormSection;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Group;

class SplitCustomFormRender
{

    public static function renderFormSection(AProductFormSection $section, CustomForm $form, string $viewMode) : array{
        $sectionField = self::getFields($form, $section);

        if(is_null($sectionField)) return [];
        $customFields = $sectionField->allCustomFieldsInLayout()->with([
            "customOptions",
            "generalField.customOptions",
            "generalField"
        ])->get();

        $render= CustomFormRender::getFormRender($viewMode,$form);
        $customFormSchema = CustomFormRender::render($sectionField->form_position,$customFields,$render,$viewMode)[0];

        return  [
            Group::make()->schema($customFormSchema)->columnSpanFull()->columns(1),
        ];

    }


    public static function generateInfoListSchema(AProductFormSection $section,  CustomFormAnswer $formAnswer, string $viewMode):array {
        $customForm = CustomForm::cached($formAnswer->custom_form_id);
        $sectionField = self::getFields($customForm, $section);
        if (is_null($sectionField)) return [];

        $customFields = $sectionField->allCustomFieldsInLayout()->with([
            "customOptions",
            "generalField.customOptions",
            "generalField"
        ])->get();

        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= CustomFormRender::getInfolistRender($viewMode,$customForm, $formAnswer,$fieldAnswers);
        return CustomFormRender::render($sectionField->form_position,$customFields,$render,$viewMode)[0];
    }


    private static function getFields(CustomForm $customForm, AProductFormSection $section): ?CustomField {
        $customFields = $customForm->cachedFields();

        /**@var CustomField $sectionField */
        $sectionField = $customFields
            ->where(fn(CustomField $field) => $field->getInheritState()["type"] == $section::getFieldIdentifier())->first();
        return $sectionField;
    }

}
