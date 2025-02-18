<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomFormPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CustomForm $customForm): bool
    {
        if ($user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS) ||
            $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS)) {
            return true;
        }

        if (empty($customForm->template_identifier)) {
            return false;
        }

        return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES);
    }

    public function viewAny(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS) ||
            $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) ||
            $user->can(CustomFormPermissionName::MANAGE_TEMPLATES);
    }

    public function create(User $user, CustomForm $customForm): bool
    {
        return $this->update($user, $customForm);
    }

    public function update(User $user, CustomForm $customForm): bool
    {
        if (!empty($customForm->template_identifier)) {
            return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES) ?? false;
        }
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) ?? false;
    }

    public function delete(User $user, CustomForm $customForm): bool
    {
        return $this->update($user, $customForm);
    }

    public function manageForms(User $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS) ?? false;
    }

    public function manageTemplates(User $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES) ?? false;
    }

    public function showResource(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS) ?? false;
    }

    public function showTemplateResource(User $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES) ?? false;
    }

}
