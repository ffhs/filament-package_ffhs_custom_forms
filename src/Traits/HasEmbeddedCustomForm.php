<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\CustomFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Facades\CustomForms;

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

    public function fillFromRelationship(): void
    {
        $data = $this->loadAnswerData($this);
        $data = $this->mutateRelationshipDataBeforeFill($data);

        $this->getChildSchema()?->fill($data, false, false);
    }

    public function getFormConfiguration(): CustomFormConfiguration
    {
        $customFormData = $this->getCustomFormData();
        $formIdentifier = $customFormData['custom_form_identifier'] ?? null;

        if (is_null($formIdentifier)) {
            throw new \RuntimeException("Missing form identifier or form data");
        }

        return CustomForms::getFormConfiguration($formIdentifier);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'viewMode' => [$this->getViewMode()],
            'customForm' => [$this->getCustomForm()],
            'customFormAnswer' => [$this->getCachedExistingRecord() ?? $this->getRecord()], //ToDo Fix
            'customFormData' => [$this->getCustomFormData()],
            'fieldDisplayer' => [$this->getFieldDisplayer()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName)
        };
    }
}
