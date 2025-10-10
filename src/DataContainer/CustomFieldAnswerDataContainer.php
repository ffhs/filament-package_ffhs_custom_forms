<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;


class CustomFieldAnswerDataContainer implements EmbedCustomFieldAnswer
{
    public function __construct(
        protected array $data,
        protected EmbedCustomFormAnswer $formAnswer,
        protected EmbedCustomField $customField
    ) {
    }

    public static function make(array $data, EmbedCustomFormAnswer $formAnswer, EmbedCustomField $customField)
    {
        return app(static::class, ['data' => $data, 'formAnswer' => $formAnswer, 'customField' => $customField]);
    }

    public function getCustomForm(): EmbedCustomForm
    {
        return $this->formAnswer->getCustomForm();
    }

    public function getCustomField(): EmbedCustomField
    {
        return $this->customField;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            default => $this->data[$name] ?? null
        };
    }

    public function getType(): CustomFieldType
    {
        return $this->getCustomField()->getType();
    }

    public function getCustomFormAnswer(): EmbedCustomFormAnswer
    {
        return $this->formAnswer;
    }

    public function getPath(): ?string
    {
        return $this->data['path'];
    }

    public function getAnswer(): mixed
    {
        return $this->data['answer'];
    }

}
