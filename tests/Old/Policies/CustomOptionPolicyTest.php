<?php


use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});

test('can view option in custom form')->todo();
test('can update/create/delete option in custom form')->todo();
test('can\'t view option in custom form')->todo();
test('can\'t update/create/delete option in custom form')->todo();


