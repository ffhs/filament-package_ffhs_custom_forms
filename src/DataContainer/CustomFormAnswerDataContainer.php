<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\DataContainer;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Illuminate\Support\Arr;
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
            ->mapWithKeys(fn(EmbedCustomField $field) => [$field->identifier() => $field]);

        foreach (Arr::dot($this->data) as $key => $fieldData) {
            $path = explode('.', $key);

            // Entferne alle Array-Elemente nach "saved"
            $savedIndex = array_search('saved', $path);
            if ($savedIndex !== false) {
                $path = array_slice($path, 0, $savedIndex);
            }

            $pathKey = implode('.', $path);

            if (array_key_exists($pathKey, $answer)) {
                continue;
            }


            $identifier = array_pop($path);
            $path = implode('.', $path);
            $path = empty($path) ? null : $path;

            $field = $customFields->get($identifier);

            if (is_null($field)) {
                continue;
            }

            $newData = ['answer' => Arr::get($this->data, $pathKey), 'path' => $path];
            $answer[$pathKey] = CustomFieldAnswerDataContainer::make($newData, $this, $field);
        }

        return collect(array_values($answer));
    }

    public function getCustomForm(): EmbedCustomForm
    {
        return $this->customForm;
    }
}
