<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\LayoutType\CustomLayoutType;
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
        $customFields = $customForm->customFields;

        /**@var null|CustomField $layoutField */
        $layoutField = $customFields
            ->filter(fn(CustomField $field) => $field->type == $layoutType::identifier())
            ->first();


        if (is_null($layoutField)) return [];


        $customFields = $layoutField->customForm->getOwnedFields()
            ->where("form_position", ">", $layoutField->form_position)
            ->where("form_position", "<=", $layoutField->layout_end_position);
        //ToDo Check if it works fine ^^ $customFieldsOld = $layoutField->allCustomFieldsInLayout()->get();


        $output = CustomFormRender::render($layoutField->form_position,$customFields,$render,$viewMode, $customForm);
        if(empty($output)) return [];
        return $output[0];
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
            ->get();

        return CustomFormRender::render($formBeginPos-1,$customFields,$render,$viewMode, $customForm)[0];
    }


    public static function renderFormFromField(CustomField $field, string $viewMode = "default") : array{
        $render= CustomFormRender::getFormRender($viewMode,$field->customForm);
        return self::renderField($field,$render,$viewMode);
    }

    public static function renderInfolistFromField(CustomField $fiel,CustomFormAnswer $formAnswer,  string $viewMode  = "default"):array {
        $fieldAnswers = $formAnswer->customFieldAnswers;

        $render= CustomFormRender::getInfolistRender($viewMode,$formAnswer->customForm, $formAnswer,$fieldAnswers);

        return self::renderField($fiel,$render,$viewMode);
    }

    protected static function renderField(CustomField $field, Closure $render, string $viewMode  = "default"){
        $customForm = $field->customForm;
        $endPos = $field->layout_end_position;
        $beginPos = $field->form_position;
        if($endPos == 0){
            $endPos = $beginPos;
            $beginPos -= 1;
        }

        return self::renderPose($beginPos,$endPos,$customForm,$render,$viewMode);
    }


}
