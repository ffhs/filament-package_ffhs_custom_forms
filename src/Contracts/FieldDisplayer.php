<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;


use Ffhs\FilamentPackageFfhsCustomForms\Enums\FormRuleAction;

interface FieldDisplayer
{
    public function __invoke(string $viewMode, EmbedCustomField $customField, array $parameter);

    public function getRuleActionBeforeRender(): FormRuleAction;

    public function getRuleActionAfterRender(): FormRuleAction;
}
