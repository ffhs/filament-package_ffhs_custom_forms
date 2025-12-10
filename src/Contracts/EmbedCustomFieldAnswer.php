<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;


use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;

interface EmbedCustomFieldAnswer
{
    public function getCustomForm(): EmbedCustomForm;

    public function getCustomField(): EmbedCustomField;

    public function getType(): CustomFieldType;

    public function getCustomFormAnswer(): EmbedCustomFormAnswer;

    public function getPath(): ?string;

    public function getAnswer(): mixed;
}
