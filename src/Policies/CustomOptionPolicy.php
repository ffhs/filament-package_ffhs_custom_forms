<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CustomOptionPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, CustomOption $option): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new CustomFormPolicy()->viewAny($user);
    }

    public function create(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }

    public function delete(Authorizable $user, CustomOption $option): bool
    {
        return $this->update($user, $option);
    }

    public function update(Authorizable $user, CustomOption $option): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }
}
