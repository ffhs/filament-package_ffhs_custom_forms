<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class GeneralFieldPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, GeneralField $generalField): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new CustomFormPolicy()->viewAny($user)
            || $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS->value);
    }

    public function filamentResource(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS->value);
    }

    public function create(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS->value);
    }

    public function delete(Authorizable $user, GeneralField $generalField): bool
    {
        return $this->update($user, $generalField);
    }

    public function update(Authorizable $user, GeneralField $generalField): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS->value);
    }
}
