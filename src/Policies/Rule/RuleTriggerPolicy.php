<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use App\Models\User;
use Ffhs\FfhsUtils\Models\RuleTrigger;
use Illuminate\Auth\Access\HandlesAuthorization;


class RuleTriggerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, RuleTrigger $ruleTrigger): bool
    {
        return (new RulePolicy())->view($user, $ruleTrigger->rule);
    }

    public function viewAny(User $user): bool
    {
        return (new RulePolicy())->viewAny($user);
    }

    public function create(User $user, RuleTrigger $ruleTrigger): bool
    {
        return $this->update($user, $ruleTrigger);
    }

    public function update(User $user, RuleTrigger $ruleTrigger): bool
    {
        return (new RulePolicy())->update($user, $ruleTrigger->rule);
    }

    public function delete(User $user, RuleTrigger $ruleTrigger): bool
    {
        return $this->update($user, $ruleTrigger);
    }


}
