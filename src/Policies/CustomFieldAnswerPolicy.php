<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Policies;


use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CustomFieldAnswerPolicy
{
    use HandlesAuthorization;

    public function view(Authorizable $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return $this->viewAny($user);
    }

    public function viewAny(Authorizable $user): bool
    {
        return new CustomFormAnswerPolicy()->viewAny($user);
    }

    public function create(Authorizable $user): bool
    {
        return new CustomFormAnswerPolicy()->create($user);
    }

    public function delete(Authorizable $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return $this->update($user, $customFieldAnswer);
    }

    public function update(Authorizable $user, CustomFieldAnswer $customFieldAnswer): bool
    {
        return new CustomFormAnswerPolicy()->update($user, $customFieldAnswer->customFormAnswer);
    }
}
