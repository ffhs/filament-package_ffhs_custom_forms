<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomLayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;

class SplitCustomFormRender
{

    public static function renderFormLayoutType(CustomLayoutType $layoutType, CustomForm $customForm, string $viewMode = "default") : array{
        $render= CustomFormRender::getFormRender($viewMode,$customForm);
        return self::renderLayoutType($layoutType,$customForm,$render,$viewMode);
    }

    public static function renderInfoListLayoutType(CustomLayoutType $layoutType,  CustomFormAnswer $formAnswer, string $viewMode = "default"):array {
        $customForm = CustomForm::cached($formAnswer->custom_form_id);
        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= CustomFormRender::getInfolistRender($viewMode,$customForm, $formAnswer,$fieldAnswers);

        return self::renderLayoutType($layoutType,$customForm,$render,$viewMode);
    }


    protected static function renderLayoutType(CustomLayoutType $layoutType,  CustomForm $customForm, Closure $render, string $viewMode = "default"){
        $customFields = $customForm->cachedFields();

        /**@var null|CustomField $layoutField */
        $layoutField = $customFields
            ->filter(fn(CustomField $field) => $field->getInheritState()["type"] == $layoutType::getFieldIdentifier())
            ->first();

        if (is_null($layoutField)) return [];

        $customFields = $layoutField->allCustomFieldsInLayout()->with([
            "customOptions",
            "generalField.customOptions",
            "generalField"
        ])->get();

        return CustomFormRender::render($layoutField->form_position,$customFields,$render,$viewMode)[0];
    }


    public static function renderFormPose(int $formBeginPos, int $formEndPos, CustomForm $customForm, string $viewMode = "default") : array{
        $render= CustomFormRender::getFormRender($viewMode,$customForm);
        return self::renderPose($formBeginPos,$formEndPos,$customForm,$render,$viewMode);
    }

    public static function renderInfolistPose(int $formBeginPos, int $formEndPos, CustomFormAnswer $formAnswer, string $viewMode  = "default"):array {
        $customForm = CustomForm::cached($formAnswer->custom_form_id);
        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= CustomFormRender::getInfolistRender($viewMode,$customForm, $formAnswer,$fieldAnswers);

        return self::renderPose($formBeginPos,$formEndPos,$customForm,$render,$viewMode);
    }


    protected static function renderPose(int $formBeginPos, int $formEndPos, CustomForm $customForm, Closure $render, string $viewMode  = "default"){
        $customFields = $customForm
            ->customFields()
            ->where("form_position",">=",$formBeginPos)
            ->where("layout_end_position", "<=", $formEndPos)
            ->with([
                "customOptions",
                "generalField.customOptions",
                "generalField"
            ])
            ->get();

        return CustomFormRender::render($formBeginPos-1,$customFields,$render,$viewMode)[0];
    }


}
