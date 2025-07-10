<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;


class CustomFormAnswerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function create(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function delete(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $this->update($user, $customFormAnswer);
    }

    public function update(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS);
    }

    public function showResource(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORM_ANSWERS) ?? false;
    }
}
