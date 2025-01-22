<?php

use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\FormConverter\SchemaExporter\FormSchemaExporter;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\FormConverter\FormConverterCase;
use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\SplittedType\Types\RepeaterLayoutType;
use Workbench\App\Models\UserSuperAdmin;


beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($user);

    /**@var CustomForm $customForm*/
    $customForm = CustomForm::create([
        'short_title' => 'My custom form title',
        'custom_form_identifier' => 'test_form_identifier',
    ]);

    $customField = CustomField::create([
        'name' => ['de' => 'test_field'],
        'form_position' => 1,
        'identifier' => uniqid(),
        'type' => RepeaterLayoutType::identifier(),
        'options' => [],
    ]);


//    $customForm->ownedFields()->save($customField);
});

test('repeater title', function () {



})->only();
