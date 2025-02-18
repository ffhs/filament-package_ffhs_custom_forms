<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomOptionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CustomOption $option): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return (new CustomFormPolicy())->viewAny($user);
    }

    public function create(User $user, CustomOption $option): bool
    {
        return $this->update($user, $option);
    }

    public function update(User $user, CustomOption $customField): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) || $user->can(
                CustomFormPermissionName::MANAGE_TEMPLATES
            );
    }

    public function delete(User $user, CustomOption $customField): bool
    {
        return $this->update($user, $option);
    }

}
