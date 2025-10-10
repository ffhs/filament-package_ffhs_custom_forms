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
        return $customField
            ->getType()
            ->getFormComponent($customField, $this->form->getFormConfiguration(), $viewMode, $parameter);
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
