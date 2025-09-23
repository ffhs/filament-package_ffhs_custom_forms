<?php


use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});


test('can view form rule in custom form')->todo();
test('can update/create/delete form rule in custom form')->todo();
test('can\'t view form rule in custom form')->todo();
test('can\'t update/create/delete form rule in custom form')->todo();

test('can view form rule in template')->todo();
test('can update/create/delete form rule in template')->todo();
test('can\'t view form rule in template')->todo();
test('can\'t update/create/delete form rule in template')->todo();
