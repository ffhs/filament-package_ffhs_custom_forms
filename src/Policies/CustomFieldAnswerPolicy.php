<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;


class CustomFieldAnswerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(User $user): bool
    {
        return (new CustomFormAnswerPolicy())->viewAny($user);
    }

    public function create(User $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return $this->update($user, $customFieldAnswer);
    }

    public function update(User $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return (new CustomFormAnswerPolicy())->update($user, $customFieldAnswer->customFormAnswer);
    }

    public function delete(User $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return $this->update($user, $customFieldAnswer);
    }


}
