<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFieldAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFieldAnswerDataContainer;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntryFieldDisplayer implements FieldDisplayer
{
    protected Collection $fieldAnswers;

    public function __construct(protected EmbedCustomFormAnswer $customFormAnswer, protected ?string $path = null)
    {
        $this->fieldAnswers = $customFormAnswer
            ->getCustomFieldAnswers()
            ->filter(function (EmbedCustomFieldAnswer $item) use ($path) {
                if (is_null($path) || is_null($item->getPath())) {
                    return is_null($item->getPath()) && is_null($path);
                }

                return str_contains($item->getPath(), $path);
            })
            ->mapWithKeys(function (EmbedCustomFieldAnswer $item) {
                return [$item->getCustomField()->identifier => $item];
            });
    }

    public static function make(EmbedCustomFormAnswer $customFormAnswer, ?string $path = null): static
    {
        return app(static::class, ['customFormAnswer' => $customFormAnswer, 'path' => $path]);
    }

    public function __invoke(string $viewMode, EmbedCustomField $customField, array $parameter): Component
    {
        /** @var CustomFormAnswer $answer */
        $answer = $this
            ->fieldAnswers
            ->get($customField->identifier);

        if (is_null($answer)) {
            $answer = CustomFieldAnswerDataContainer::make(['answer' => null, 'path' => $this->path,],
                $this->customFormAnswer, $customField);
        }

        if ($answer instanceof Model && $this->customFormAnswer instanceof Model && $customField instanceof Model) {
            $answer->setRelation('customField', $customField);
            $answer->setRelation('customFormAnswer', $this->customFormAnswer);
            $answer->setRelation('customForm', $customField->customForm);
        }

        return $customField
            ->getType()
            ->getEntryComponent($answer, $viewMode, $parameter);
    }

    public function getRuleActionBeforeRender(): FormRuleAction
    {
        return FormRuleAction::BeforeRender;
    }

    public function getRuleActionAfterRender(): FormRuleAction
    {
        return FormRuleAction::AfterRenderEntry;
    }
}
