<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class FormRulePolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, FormRule $formRule): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return true;
    }

    public function create(Authorizable $user): bool
    {
        return new CustomFormPolicy()->create($user);
    }

    public function delete(Authorizable $user, FormRule $formRule): bool
    {
        return $this->update($user, $formRule);
    }

    public function update(Authorizable $user, FormRule $formRule): bool
    {
        return new CustomFormPolicy()->update($user, $formRule->customForm);
    }


}
