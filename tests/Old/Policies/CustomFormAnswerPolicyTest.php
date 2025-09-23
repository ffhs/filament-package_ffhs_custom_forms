<?php

use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\CustomFormAnswerResource;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;
use Spatie\Permission\Models\Permission;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});

describe('test filament custom form resource access', function () {
    it('no permissions forbidden', function () {
        expect(CustomFormAnswerResource::canAccess())->toBeFalse();
    });
    it(
        'permission to fill custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(CustomFormAnswerResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to show resource, but forbidden because  no permission to view any form ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORM_ANSWERS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(CustomFormAnswerResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to manage custom forms, but forbidden because  no permission to edit answer',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(CustomFormAnswerResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to show resource',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORM_ANSWERS,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(CustomFormAnswerResource::canAccess())->toBeTrue();
        }
    );
    it(
        'permission to show resource, but not tom fill',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORM_ANSWERS,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(CustomFormAnswerResource::canAccess())->toBeFalse();
        }
    );
});


test('test can\'t access custom form answer')->todo();
test('test can\'t update/delete/create custom form answer')->todo();
test('test can access custom form answer')->todo();
test('test can update/delete/create custom form answer')->todo();
