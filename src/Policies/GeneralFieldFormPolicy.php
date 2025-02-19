<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralFieldFormPolicy
{
    use HandlesAuthorization;

    public function view(User $user, GeneralFieldForm $generalFieldForm): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return (new GeneralFieldPolicy())->viewAny($user);
    }

    public function create(User $user, GeneralFieldForm $generalFieldForm): bool
    {
        return $this->update($user, $generalFieldForm);
    }

    public function update(User $user, GeneralFieldForm $generalFieldForm): bool
    {
        return (new GeneralFieldPolicy())->update($user, $generalFieldForm->generalField);
    }

    public function delete(User $user, GeneralFieldForm $generalFieldForm): bool
    {
        return $this->update($user, $generalFieldForm);
    }

}
