<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;


class CustomFieldAnswerDataContainer implements EmbedCustomFieldAnswer
{
    public function __construct(protected array $data, protected CustomFormConfiguration $customFormConfiguration)
    {
    }

    public static function make(array $data, CustomFormConfiguration $customFormConfiguration)
    {
        return app(static::class, ['data' => $data, 'customFormConfiguration' => $customFormConfiguration]);
    }
}
