<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Infolists\Components\Component;
use Illuminate\Support\Collection;

class InfolistFieldDisplayer implements FieldDisplayer
{
    protected Collection $fieldAnswers;

    public function __construct(
        protected CustomFormAnswer $formAnswer,
        protected ?string $path = null
    ) {
        $this->fieldAnswers = $formAnswer->customFieldAnswers;
        $this->fieldAnswers = $this->fieldAnswers->filter(function ($item) use ($path) {
            if (is_null($item['path']) || is_null($path)) {
                return is_null($item['path']) && is_null($path);
            }
            return str_contains($item['path'], $path);
        })->keyBy('custom_field_id');
    }

    public static function make(
        CustomFormAnswer $formAnswer,
        ?string $path = null
    ): static {
        return app(__CLASS__, ['formAnswer' => $formAnswer, 'path' => $path]);
    }

    public function __invoke(string $viewMode, CustomField $customField, array $parameter): Component
    {
        /** @var CustomFormAnswer $answer */
        $answer = $this->fieldAnswers->get($customField->id);
        if (is_null($answer)) {
            $answer = new CustomFieldAnswer();
            $answer->answer = null;
            $answer->custom_field_id = $customField->id;
            $answer->custom_form_answer_id = $this->formAnswer->id;
            $answer->path = $this->path;
        }

        $answer->setRelation('customForm', $customField->customForm);
        return $customField->getType()
            ->getInfolistComponent($answer, $viewMode, $parameter);
    }
}
