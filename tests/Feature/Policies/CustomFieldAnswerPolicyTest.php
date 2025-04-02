<?php

use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});

test('test can\'t access field answare in form answer')->todo();
test('test can\'t update/delete/create cfield answare in form answer')->todo();
test('test can access field answare in form answer')->todo();
test('test can update/delete/create field answare in form answer')->todo();
