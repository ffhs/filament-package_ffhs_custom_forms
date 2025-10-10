<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use Ffhs\FfhsUtils\Models\RuleEvent;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;


class RuleEventPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, RuleEvent $ruleEvent): bool
    {
        return new RulePolicy()->view($user, $ruleEvent->rule);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new RulePolicy()->viewAny($user);
    }

    public function create(Authorizable $user, RuleEvent $ruleEvent): bool
    {
        return $this->update($user, $ruleEvent);
    }

    public function update(Authorizable $user, RuleEvent $ruleEvent): bool
    {
        return new RulePolicy()->update($user, $ruleEvent->rule);
    }

    public function delete(Authorizable $user, RuleEvent $ruleEvent): bool
    {
        return $this->update($user, $ruleEvent);
    }


}
