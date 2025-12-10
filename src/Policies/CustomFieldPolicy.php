<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CustomFieldPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, CustomField $customField): bool
    {
        return new CustomFormPolicy()->view($user, $customField->customForm);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new CustomFormPolicy()->viewAny($user);
    }

    public function create(Authorizable $user): bool
    {
        return new CustomFormPolicy()->create($user);
    }

    public function update(Authorizable $user, CustomField $customField): bool
    {
        return new CustomFormPolicy()->update($user, $customField->customForm);
    }

    public function delete(Authorizable $user, CustomField $customField): bool
    {
        return new CustomFormPolicy()->delete($user, $customField->customForm);
    }
}
