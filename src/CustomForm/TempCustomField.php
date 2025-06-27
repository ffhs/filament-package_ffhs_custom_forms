<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\CustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\TemplatesType\TemplateFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;

class TempCustomField
{
    protected ?GeneralField $generalField;
    protected ?CustomForm $customForm;
    protected ?CustomForm $template;

    public function __construct(protected CustomForm $referenceForm, protected array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getGeneralField(): ?GeneralField
    {
        if (!isset($this->generalField)) {
            if (empty($this->data['general_field_id'])) {
                $this->generalField = null;

                return $this->generalField;
            }

            $genField = $this
                ->referenceForm
                ->getFormConfiguration()
                ->getAvailableGeneralFields()
                ->get($this->data['general_field_id']);
            $this->generalField = $genField;
        }

        return $this->generalField;
    }

    public function getCustomForm(): ?CustomForm
    {
        if (!isset($this->customForm)) {
            if ($this->data['custom_form_id'] === $this->referenceForm->id) {
                $this->customForm = $this->referenceForm;
            } else {
                $form = $this
                    ->referenceForm
                    ->getFormConfiguration()
                    ->getAvailableTemplates()
                    ->get($this->data['custom_form_id']);

                if ($form) {
                    $this->customForm = $form;
                }
            }
        }

        return $this->customForm;
    }

    public function getTemplate(): ?CustomForm
    {
        if (!isset($this->template)) {
            if (!empty($this->data['template_id'])) {
                $form = $this
                    ->referenceForm
                    ->getFormConfiguration()
                    ->getAvailableTemplates()
                    ->get($this->data['template_id']);
                $this->template = $form;

                return $this->template;
            }

            $this->template = null;
        }

        return $this->template;
    }

    public function identifier(): string
    {
        if (!empty($this->data['general_field_id'])) {
            return $this
                ->getGeneralField()
                ->identifier;
        }

        return $this->getData()['identifier'] ?? '';
    }

    public function getType(): CustomFieldType
    {
        if ($this->data['template_id']) {
            return TemplateFieldType::make();
        }

        $typeName = $this->data['type'];

        return CustomFieldType::getTypeClassFromIdentifier($typeName)::make();
    }
}
