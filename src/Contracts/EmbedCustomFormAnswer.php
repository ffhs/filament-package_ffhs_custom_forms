<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Contracts;


use Illuminate\Support\Collection;

interface EmbedCustomFormAnswer
{
    public function getCustomFieldAnswers(): Collection;

    public function getCustomForm(): EmbedCustomForm;

}
