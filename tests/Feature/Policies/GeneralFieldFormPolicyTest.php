<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;

pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});


test('can view general field form')->todo();
test('can update/create/delete general field form')->todo();

test('can\'t view general field form')->todo();
test('can\'t update/create/delete general field form')->todo();
