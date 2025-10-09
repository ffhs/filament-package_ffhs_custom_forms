<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralFieldForm;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class GeneralFieldFormPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, GeneralFieldForm $generalFieldForm): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new GeneralFieldPolicy()->viewAny($user);
    }

    public function create(Authorizable $user): bool
    {
        return new GeneralFieldPolicy()->create($user);
    }

    public function delete(Authorizable $user, GeneralFieldForm $generalFieldForm): bool
    {
        return $this->update($user, $generalFieldForm);
    }

    public function update(Authorizable $user, GeneralFieldForm $generalFieldForm): bool
    {
        return new GeneralFieldPolicy()->update($user, $generalFieldForm->generalField);
    }
}
