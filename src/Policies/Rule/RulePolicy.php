<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies\Rule;

use Ffhs\FfhsUtils\Models\Rule;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;


class RulePolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, Rule $rule): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) || $user->can( //ToDO do for rule only
                CustomFormPermissionName::MANAGE_TEMPLATES
            );
    }

    public function create(Authorizable $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }

    public function update(Authorizable $user, Rule $rule): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) || $user->can( //ToDO do for rule only
                CustomFormPermissionName::MANAGE_TEMPLATES
            );
    }

    public function delete(Authorizable $user, Rule $rule): bool
    {
        return $this->update($user, $rule);
    }


}
