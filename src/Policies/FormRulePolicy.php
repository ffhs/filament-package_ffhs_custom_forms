<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\FormRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormRulePolicy
{
    use HandlesAuthorization;

    public function view(User $user, FormRule $formRule): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return (new FormRulePolicy())->viewAny($user);
    }

    public function create(User $user): bool
    {
        return (new CustomFormPolicy())->create($user);
    }

    public function delete(User $user, FormRule $formRule): bool
    {
        return $this->update($user, $formRule);
    }

    public function update(User $user, FormRule $formRule): bool
    {
        return (new CustomFormPolicy())->update($user, $formRule->customForm);
    }


}
