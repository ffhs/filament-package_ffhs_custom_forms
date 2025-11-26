<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render;

use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\EmbedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Contracts\FieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;
use Filament\Support\Components\Component;

class FormFieldDisplayer implements FieldDisplayer
{
    public function __construct(protected EmbedCustomForm $form)
    {
    }

    public static function make(EmbedCustomForm $customForm): static
    {
        return app(static::class, ['form' => $customForm]);
    }

    public function __invoke(string $viewMode, EmbedCustomField $customField, array $parameter): Component
    {
        $formConfiguration = $this->form->getFormConfiguration();
        $fieldType = $customField->getType();
//        \Debugbar::startMeasure('test-' . $fieldType::identifier());
        $schema = $fieldType->getFormComponent($customField, $formConfiguration, $viewMode, $parameter);
//        \Debugbar::stopMeasure('test-' . $fieldType::identifier());
        return $schema;
    }

    public function getRuleActionBeforeRender(): FormRuleAction
    {
        return FormRuleAction::BeforeRender;
    }

    public function getRuleActionAfterRender(): FormRuleAction
    {
        return FormRuleAction::AfterRenderForm;
    }
}
