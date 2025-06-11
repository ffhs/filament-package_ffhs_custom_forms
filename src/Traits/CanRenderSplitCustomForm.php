<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\LayoutType\CustomLayoutType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;

trait CanRenderSplitCustomForm
{
    use CanRenderCustomForm;

    protected function renderLayoutTypeSplit(
        CustomLayoutType $layoutType,
        CustomForm $customForm,
        FieldDisplayer $displayer,
        string $viewMode
    ) {
        /**@var null|CustomField $layoutField */
        $customFields = $customForm->customFields;
        $layoutField = $customFields->firstWhere(fn(CustomField $field) => $field->type === $layoutType::identifier());

        if (is_null($layoutField)) {
            return [];
        }

        $positionOffset = $layoutField->form_position;
        $customFields = $layoutField->customForm->getOwnedFields()
            ->where('form_position', '>', $layoutField->form_position)
            ->where('form_position', '<=', $layoutField->layout_end_position);

        return $this->renderCustomForm($viewMode, $displayer, $customForm, $customFields, $positionOffset)[0];
    }

    protected function renderPose(
        int $formBeginPos,
        int $formEndPos,
        CustomForm $customForm,
        FieldDisplayer $displayer,
        string $viewMode
    ) {

        $positionOffset = $formBeginPos - 1;
        $customFields = $customForm
            ->customFields()
            ->where('form_position', '>=', $formBeginPos)
            ->where('layout_end_position', '<=', $formEndPos)
            ->get();

        return $this->renderCustomForm($viewMode, $displayer, $customForm, $customFields, $positionOffset)[0];
    }

    protected function renderField(
        CustomField $field,
        CustomForm $customForm,
        FieldDisplayer $displayer,
        string $viewMode
    ) {
        $endPos = $field->layout_end_position;
        $beginPos = $field->form_position;

        if ($endPos === 0) {
            $endPos = $beginPos;
            --$beginPos;
        }

        return $this->renderPose($beginPos, $endPos, $customForm, $displayer, $viewMode);
    }
}
