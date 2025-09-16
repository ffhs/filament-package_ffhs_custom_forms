<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\DataContainer\CustomFormAnswerDataContainer;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Illuminate\Database\Eloquent\Model;

trait HasEmbeddedCustomForm
{
    use CanLoadFormAnswer;
    use UseSplitSchema;
    use UseSplitCustomForm;
    use HasFieldDisplayer;
    use HasCustomFormData;
    use HasViewMode;
    use HasFormConfiguration {
        HasFormConfiguration::getFormConfiguration as getFormConfigurationFromParent;
    }
    use EntanglesStateWithSingularRelationship {
        EntanglesStateWithSingularRelationship::relationship as parentRelationship;
    }

    public function relationship(
        string $name,
        bool|Closure $condition = true,
        Closure|string|null $relatedModel = null
    ): static {
        return $this->parentRelationship($name, $condition, $relatedModel)
            ->customForm(fn() => $this->getCachedExistingRecord()?->customForm);
    }

    public function fillFromRelationship(): void
    {
        $data = $this->loadAnswerData($this);
        $data = $this->mutateRelationshipDataBeforeFill($data);

        $this->getChildSchema()?->fill($data, false, false);
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        $formConfiguration = $this->getCustomForm()?->getFormConfiguration();

        if (is_null($formConfiguration)) {
            throw new \RuntimeException('Missing form identifier or form data');
        }

        return $formConfiguration;
    }

    public function getCustomFormAnswer(): EmbedCustomFormAnswer|Model
    {
        if ($this->relationship) {
            return $this->getCachedExistingRecord(); //toDo maby create new record if none exist
        }

        return CustomFormAnswerDataContainer::make($this->getState() ?? [], $this->getCustomForm());
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'viewMode' => [$this->getViewMode()],
            'customForm' => [$this->getCustomForm()],
            'customFormAnswer' => [$this->getCustomFormAnswer()],
            'customFormData' => [$this->getCustomFormData()],
            'fieldDisplayer' => [$this->getFieldDisplayer()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName)
        };
    }
}
