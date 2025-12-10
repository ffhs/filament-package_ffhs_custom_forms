<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;


use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CustomFormPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, CustomForm $customForm): bool
    {
        if ($user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value)) {
            return true;
        }

        if (empty($customForm->template_identifier)) {
            return false;
        }

        return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILL_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }

    public function create(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value)
            || $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }

    public function delete(Authorizable $user, CustomForm $customForm): bool
    {
        return $this->update($user, $customForm);
    }

    public function update(Authorizable $user, CustomForm $customForm): bool
    {
        if (!empty($customForm->template_identifier)) {
            return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
        }

        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value);
    }

    public function manageForms(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_CUSTOM_FORMS->value);
    }

    public function manageTemplates(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::MANAGE_TEMPLATES->value);
    }

    public function showResource(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS->value);
    }

    public function showTemplateResource(Authorizable $user): bool
    {
        return $user->can(CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES->value);
    }
}
