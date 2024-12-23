<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomFormPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_custom_form') ?? false;
    }

    public function view(User $user, CustomForm $customForm): bool
    {
        return $user->can('view_custom_form') ?? false;
    }

    public function update(User $user, CustomForm $customForm): bool
    {
        return $user->can('update_custom_form') ?? false;
    }

    public function create(User $user, CustomForm $customForm): bool
    {
        return $user->can('create_custom_form') ?? false;
    }

    public function delete(User $user, CustomForm $customForm): bool
    {
        return $user->can('delete_custom_form') ?? false;
    }
}
