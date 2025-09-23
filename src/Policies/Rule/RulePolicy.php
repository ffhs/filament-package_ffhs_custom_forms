<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use App\Models\User;
use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
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
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) || $user->can( //ToDO do for rule only
                CustomFormPermissionName::MANAGE_TEMPLATES
            );
    }

    public function create(User $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }

    public function update(User $user, Rule $rule): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) || $user->can( //ToDO do for rule only
                CustomFormPermissionName::MANAGE_TEMPLATES
            );
    }

    public function delete(User $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }


}
