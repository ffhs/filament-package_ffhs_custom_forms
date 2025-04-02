<?php

namespace Workbench\App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserSuperAdmin extends \App\Models\User
{
    protected $table = 'users';
    public function hasSuperAdminRole(){
        return true;
    }
}
