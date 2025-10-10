<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use Ffhs\FfhsUtils\Models\RuleTrigger;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;


class RuleTriggerPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, RuleTrigger $ruleTrigger): bool
    {
        return new RulePolicy()->view($user, $ruleTrigger->rule);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new RulePolicy()->viewAny($user);
    }

    public function create(Authorizable $user, RuleTrigger $ruleTrigger): bool
    {
        return $this->update($user, $ruleTrigger);
    }

    public function update(Authorizable $user, RuleTrigger $ruleTrigger): bool
    {
        return new RulePolicy()->update($user, $ruleTrigger->rule);
    }

    public function delete(Authorizable $user, RuleTrigger $ruleTrigger): bool
    {
        return $this->update($user, $ruleTrigger);
    }
}
