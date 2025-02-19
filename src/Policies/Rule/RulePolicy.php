<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\Rules\Rule;
use Illuminate\Auth\Access\HandlesAuthorization;


class RulePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Rule $rule): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return true; //ToDo how to make policy
    }

    public function create(User $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }

    public function update(User $user, Rule $rule): bool
    {
        return true; //ToDo how to make policy
    }

    public function delete(User $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }


}
