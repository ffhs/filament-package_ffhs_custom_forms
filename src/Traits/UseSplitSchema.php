<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Illuminate\Support\HtmlString;

trait UseSplitSchema
{
    use CanRenderSplitCustomForm;
    use HasLayoutSplit;
    use HasFieldSplit;
    use HasPosSplit;

    /**
     * @param EmbedCustomFormAnswer $answer
     * @return array<string, mixed>
     */
    public function loadAnswerData(EmbedCustomFormAnswer $answer): array
    {
        if ($this->isUseLayoutTypeSplit()) {
            return $this->loadLayoutTypeSplitAnswerData($answer);
        }

        if ($this->isUseFieldSplit()) {
            return $this->loadFieldTypeSplitAnswerData($answer);
        }

        if ($this->isUsePoseSplit()) {
            return $this->loadPosTypeSplitAnswerData($answer);
        }

        return $this->loadCustomAnswerData($answer);
    }

    public function getDefaultChildComponents(): array
    {
        return once(function (): array {
            return $this->getCustomFormSchema($this->getCustomForm(), $this->getFieldDisplayer(), $this->getViewMode());
        });
    }

    /**
     * @param EmbedCustomForm $customForm
     * @param FieldDisplayer $fieldDisplayer
     * @param string $viewMode
     * @return array<int|string, Component|HtmlString|string>
     */
    public function getCustomFormSchema(
        EmbedCustomForm $customForm,
        FieldDisplayer $fieldDisplayer,
        string $viewMode = 'default'
    ): array {
        if ($this->isUseLayoutTypeSplit()) {
            return $this->getLayoutTypeSplitFormSchema($customForm, $fieldDisplayer, $viewMode);
        }

        if ($this->isUseFieldSplit()) {
            return $this->getFieldSplitFormSchema($customForm, $fieldDisplayer, $viewMode);
        }

        if ($this->isUsePoseSplit()) {
            return $this->getPosSplitFormSchema($customForm, $fieldDisplayer, $viewMode);
        }

        return $this->getDefaultFormSchema($customForm, $fieldDisplayer, $viewMode);
    }

    /**
     * @param EmbedCustomForm $customForm
     * @param FieldDisplayer $fieldDisplayer
     * @param string $viewMode
     * @return array<int|string, Component|HtmlString|string>
     */
    protected function getDefaultFormSchema(
        EmbedCustomForm $customForm,
        FieldDisplayer $fieldDisplayer,
        string $viewMode = 'default'
    ): array {
        $columns = $customForm->getFormConfiguration()->getColumns();
        $customFields = $customForm->getOwnedFields();

        $renderOutput = $this->renderCustomForm($viewMode, $fieldDisplayer, $customForm, $customFields);

        return [
            Group::make($renderOutput[0])->columns(fn(string $operation) => $operation === 'view' ? 1 : $columns),
        ];
    }

    /**
     * @param EmbedCustomForm $customForm
     * @param FieldDisplayer $fieldDisplayer
     * @param string $viewMode
     * @return array<int|string, Component|HtmlString|string>
     */
    protected function getLayoutTypeSplitFormSchema(
        EmbedCustomForm $customForm,
        FieldDisplayer $fieldDisplayer,
        string $viewMode = 'default'
    ): array {
        $layoutType = $this->getLayoutTypeSplit();

        return $this->renderLayoutTypeSplit($layoutType, $customForm, $fieldDisplayer, $viewMode);
    }

    /**
     * @param EmbedCustomForm $customForm
     * @param FieldDisplayer $fieldDisplayer
     * @param string $viewMode
     * @return array<int|string, Component|HtmlString|string>
     */
    protected function getPosSplitFormSchema(
        EmbedCustomForm $customForm,
        FieldDisplayer $fieldDisplayer,
        string $viewMode = 'default'
    ): array {
        $customFormEndPos = $this->getPoseSpiltEnd();
        $customFormBeginPos = $this->getPoseSpiltStart();

        return $this->renderPose($customFormBeginPos, $customFormEndPos, $customForm, $fieldDisplayer, $viewMode);
    }

    /**
     * @param EmbedCustomForm $customForm
     * @param FieldDisplayer $fieldDisplayer
     * @param string $viewMode
     * @return array<int|string, Component|HtmlString|string>
     */
    protected function getFieldSplitFormSchema(
        EmbedCustomForm $customForm,
        FieldDisplayer $fieldDisplayer,
        string $viewMode = 'default'
    ): array {
        $field = $this->getFieldSplit();

        if (is_null($field)) {
            return [];
        }

        return $this->renderField($field, $customForm, $fieldDisplayer, $viewMode);
    }
}
