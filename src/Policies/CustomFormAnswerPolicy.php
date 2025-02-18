<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;

/**@deprecated */
class CustomFormAnswerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_custom_form_answer') ?? false;
    }

    public function view(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can('view_custom_form_answer') ?? false;
    }

    public function update(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can('update_custom_form_answer') ?? false;
    }

    public function create(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can('create_custom_form_answer') ?? false;
    }

    public function delete(User $user, CustomFormAnswer $customFormAnswer): bool
    {
        return $user->can('delete_custom_form_answer') ?? false;
    }
}
