<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Spatie\Permission\Models\Role;
use Workbench\App\FFHs\TestDynamicFormConfiguration;


beforeEach(function () {
    $this->user = \App\Models\User::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($this->user);

    $this->role = Role::create([
        'name' => 'tester_role',
        'guard_name' => 'web',
    ]);
    $this->user->assignRole('tester_role');

    $this->customForm = new CustomForm([
        'short_title' => 'testForm',
        'custom_form_identifier' => TestDynamicFormConfiguration::identifier()
    ]);

    $this->customForm->save();
});


test('can view custom field in custom form')->todo();
test('can update/create/delete custom field in custom form')->todo();
test('can\'t view custom field in custom form')->todo();
test('can\'t update/create/delete custom field in custom form')->todo();

test('can view custom field in template')->todo();
test('can update/create/delete custom field in template')->todo();
test('can\'t view custom field in template')->todo();
test('can\'t update/create/delete custom field in template')->todo();
