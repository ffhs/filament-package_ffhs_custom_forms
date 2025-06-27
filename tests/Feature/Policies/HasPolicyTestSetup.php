<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies;

use App\Models\User;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Spatie\Permission\Models\Role;
use Workbench\App\FFHs\TestCustomFormConfiguration;

trait HasPolicyTestSetup
{
    protected User $user;
    protected GeneralField $genField;
    protected CustomForm $customForm;

    public function beforeEachPolicy(): void
    {
        $this->user = User::create([
            'name' => 'tester',
            'email' => 'testing@test.com',
            'password' => '1234'
        ]);
        $this->actingAs($this->user);

        $this->role = Role::create([
            'name' => 'tester_role',
            'guard_name' => 'web',
        ]);

        $this->genField = GeneralField::create([
            'identifier' => 'test',
            'is_active' => true,
            'name' => 'test',
            'type' => TextType::identifier(),
            'icon' => TextType::make()->icon(),
        ]);

        $this->user->assignRole('tester_role');

        $this->customForm = new CustomForm([
            'short_title' => 'testForm',
            'custom_form_identifier' => TestCustomFormConfiguration::identifier()
        ]);

        $this->customForm->save();
    }
}
