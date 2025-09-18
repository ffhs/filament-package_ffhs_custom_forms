<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use App\Models\User;
use Ffhs\FfhsUtils\Models\RuleEvent;
use Illuminate\Auth\Access\HandlesAuthorization;


class RuleEventPolicy
{
    use HandlesAuthorization;

    public function view(User $user, RuleEvent $ruleEvent): bool
    {
        return (new RulePolicy())->view($user, $ruleEvent->rule);
    }

    public function viewAny(User $user): bool
    {
        return (new RulePolicy())->viewAny($user);
    }

    public function create(User $user, RuleEvent $ruleEvent): bool
    {
        return $this->update($user, $ruleEvent);
    }

    public function update(User $user, RuleEvent $ruleEvent): bool
    {
        return (new RulePolicy())->update($user, $ruleEvent->rule);
    }

    public function delete(User $user, RuleEvent $ruleEvent): bool
    {
        return $this->update($user, $ruleEvent);
    }


}
