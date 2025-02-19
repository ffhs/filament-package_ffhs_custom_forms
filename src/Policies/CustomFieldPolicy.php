<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomFieldPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CustomField $customField): bool
    {
        return (new CustomFormPolicy())->view($user, $customField->customForm);
    }

    public function viewAny(User $user): bool
    {
        return (new CustomFormPolicy())->viewAny($user);
    }

    public function create(User $user): bool
    {
        return (new CustomFormPolicy())->create($user);
    }

    public function update(User $user, CustomField $customField): bool
    {
        return (new CustomFormPolicy())->update($user, $customField->customForm);
    }

    public function delete(User $user, CustomField $customField): bool
    {
        return (new CustomFormPolicy())->delete($user, $customField->customForm);
    }

}
