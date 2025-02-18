<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\CustomField\CustomFieldType\GenericType\Types\TextType;
use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\GeneralFieldResource;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


beforeEach(function () {
    $this->user = \App\Models\User::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($this->user);

    $this->genField = GeneralField::create([
        'identifier' => 'test',
        'is_active' => true,
        'name' => 'test',
        'type' => TextType::identifier(),
        'icon' => TextType::make()->icon(),
    ]);

    $this->role = Role::create([
        'name' => 'tester_role',
        'guard_name' => 'web',
    ]);
    $this->user->assignRole('tester_role');
});

describe('test filament general field resource access', function () {
    it('no permissions forbidden', function () {
        expect(GeneralFieldResource::canAccess())->toBeFalse();
    });
    it(
        'permission to view general fields, but not the resource',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(GeneralFieldResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to show resource, but forbidden because  no permission to view any form ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(GeneralFieldResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to manage custom field, no permission to access the resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_GENERAL_FIELDS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(GeneralFieldResource::canAccess())->toBeFalse();
        }
    );
    it(
        'permission to show resource (With forms)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess())->toBeTrue();
        }
    );
    it(
        'permission to show resource (With manage)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess())->toBeTrue();
        }
    );

    it(
        'permission to show resource (With template)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::MANAGE_TEMPLATES
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess())->toBeTrue();
        }
    );


    it(
        'permission to show resource (With manage form)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess())->toBeTrue();
        }
    );
});

describe('test filament general fields edit page access', function () {
    it('no permissions forbidden', function () {
        expect(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
    });
    it(
        'permission to edit general fields, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
        }
    );
    it(
        'permission to show resource, but forbidden because  no permission to edit ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
        }
    );
    it(
        'permission to manage general fields, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_GENERAL_FIELDS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
        }
    );
    it(
        'permission to show resource but not to edit',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
        }
    );
    it(
        'permission to show resource, but not to edit',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::MANAGE_TEMPLATES
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeFalse();
        }
    );
    it(
        'permission to show page',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_GENERAL_FIELDS,
                    CustomFormPermissionName::MANAGE_GENERAL_FIELDS
                ])->get();
            expect($permission)->count(2);
            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(GeneralFieldResource::canAccess() && GeneralFieldResource::canEdit($this->genField))->toBeTrue();
        }
    );
});


test('can view general field')->todo();
test('can update/delete/create general field')->todo();
test('can\'t view general field')->todo();
test('can\'t update/delete/create general field')->todo();
