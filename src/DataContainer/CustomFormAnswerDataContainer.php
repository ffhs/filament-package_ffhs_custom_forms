<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Illuminate\Support\Collection;


class CustomFormAnswerDataContainer implements EmbedCustomFormAnswer
{
    public function __construct(protected array $data, protected EmbedCustomForm $customForm)
    {
    }

    public static function make(array $data, EmbedCustomForm $customForm)
    {
        return app(static::class, ['data' => $data, 'customForm' => $customForm]);
    }

    public function getCustomFieldAnswers(): Collection
    {
        $answer = [];
        $customFields = $this->getCustomForm()
            ->customFields()
            ->mapWithKeys(fn(EmbedCustomField $field) => [$field->identifier => $field]);

        foreach ($this->data as $key => $fieldData) {
            $path = explode('.', $key);
            $identifier = array_pop($path);
            $path = implode('.', $path);
            $path = empty($path) ? null : $path;

            $field = $customFields->get($identifier);

            if (is_null($field)) {
                continue;
            }

            $newData = ['answer' => $fieldData, 'path' => $path];
            $answer[] = CustomFieldAnswerDataContainer::make($newData, $this, $field);
        }

        return collect($answer);
    }

    public function getCustomForm(): EmbedCustomForm
    {
        return $this->customForm;
    }
}
