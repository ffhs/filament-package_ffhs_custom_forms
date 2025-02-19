<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});

test('can view custom field in custom form')->todo();
test('can update/create/delete custom field in custom form')->todo();
test('can\'t view custom field in custom form')->todo();
test('can\'t update/create/delete custom field in custom form')->todo();

test('can view custom field in template')->todo();
test('can update/create/delete custom field in template')->todo();
test('can\'t view custom field in template')->todo();
test('can\'t update/create/delete custom field in template')->todo();
