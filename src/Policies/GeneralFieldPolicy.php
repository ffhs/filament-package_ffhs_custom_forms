<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralFieldPolicy
{
    use HandlesAuthorization;

    public function view(User $user, GeneralField $generalField): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return (new CustomFormPolicy())->viewAny($user)
            || $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS);
    }

    public function filamentResource(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS);
    }

    public function create(User $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS);
    }

    public function delete(User $user, GeneralField $generalField): bool
    {
        return $this->update($user, $generalField);
    }

    public function update(User $user, GeneralField $generalField): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_GENERAL_FIELDS);
    }
}
