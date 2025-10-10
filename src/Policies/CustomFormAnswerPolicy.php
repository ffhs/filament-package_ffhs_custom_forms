<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;


use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;


class CustomFormAnswerPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function create(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function delete(Authorizable $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $this->update($user, $customFormAnswer);
    }

    public function update(Authorizable $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function showResource(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORM_ANSWERS);
    }
}
