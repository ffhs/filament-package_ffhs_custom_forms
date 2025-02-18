<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Illuminate\Auth\Access\HandlesAuthorization;

/**@deprecated */
class GeneralFieldPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_general_field') ?? false;
    }

    public function view(User $user, GeneralField $generalField): bool
    {
        return $user->can('view_general_field') ?? false;
    }

    public function update(User $user, GeneralField $generalField): bool
    {
        return $user->can('update_general_field') ?? false;
    }

    public function create(User $user, GeneralField $generalField): bool
    {
        return $user->can('create_general_field') ?? false;
    }

    public function delete(User $user, GeneralField $generalField): bool
    {
        return $user->can('delete_general_field') ?? false;
    }
}
