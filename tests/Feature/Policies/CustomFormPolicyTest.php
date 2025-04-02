<?php


use Ffhs\FilamentPackageFfhsCustomForms\Enums\CustomFormPermissionName;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\EditTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\Policies\HasPolicyTestSetup;
use Spatie\Permission\Models\Permission;


pest()->use(HasPolicyTestSetup::class);

beforeEach(function () {
    $this->beforeEachPolicy();
});

describe('test filament custom form resource access', function () {
    it('no permissions forbidden', function () {
        expect(CustomFormResource::canAccess())->toBeFalse();
    });

    it(
        'permission to fill custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(CustomFormResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource, but forbidden because  no permission to view any form ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(CustomFormResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to manage custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(CustomFormResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource (With fill forms)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(CustomFormResource::canAccess())->toBeTrue();
        }
    );

    it(
        'permission to show resource (With manage forms)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(CustomFormResource::canAccess())->toBeTrue();
        }
    );
});

describe('test filament custom form edit page access', function () {
    it('no permissions forbidden', function () {
        expect(EditCustomForm::canAccess())->toBeFalse();
    });

    it(
        'permission to edit custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(EditCustomForm::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource, but forbidden because  no permission to edit ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(EditCustomForm::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to manage custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(EditCustomForm::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource but not to edit',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditCustomForm::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resolurce, but not to edit',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS,
                    CustomFormPermissionName::MANAGE_TEMPLATES
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show page',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditCustomForm::canAccess())->toBeTrue();
        }
    );
});

describe('test filament template resource access', function () {
    it('no permissions forbidden', function () {
        expect(TemplateResource::canAccess())->toBeFalse();
    });

    it(
        'permission to fill custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(TemplateResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource, but forbidden because  no permission to view any form ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(TemplateResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to manage templates, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_TEMPLATES);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(TemplateResource::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show template resource (With fill forms)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(TemplateResource::canAccess())->toBeTrue();
        }
    );

    it(
        'permission to show template resource (With manage templates)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::MANAGE_TEMPLATES
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(TemplateResource::canAccess())->toBeTrue();
        }
    );

    it(
        'permission to show template resource (With manage forms)',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(TemplateResource::canAccess())->toBeTrue();
        }
    );
});

describe('test filament template edit page access', function () {
    it('no permissions forbidden', function () {
        expect(EditCustomForm::canAccess())->toBeFalse();
    });

    it(
        'permission to edit template, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILL_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource, but forbidden because  no permission to edit ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::FILAMENT_RESOURCE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions()->get())->count(1)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to manage custom forms, but forbidden because  no permission to access the filament resource ',
        function () {
            $permission = Permission::query()
                ->firstWhere('name', CustomFormPermissionName::MANAGE_CUSTOM_FORMS);
            expect($permission)->not()->toBeNull();

            $this->role->permissions()->sync($permission->id);
            expect($this->role->permissions())->count(1)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resource but not to edit',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::FILL_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show resolurce, but not to edit templates',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::MANAGE_CUSTOM_FORMS
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditTemplate::canAccess())->toBeFalse();
        }
    );

    it(
        'permission to show page',
        function () {
            $permission = Permission::query()
                ->whereIn('name', [
                    CustomFormPermissionName::FILAMENT_RESOURCE_TEMPLATES,
                    CustomFormPermissionName::MANAGE_TEMPLATES
                ])->get();
            expect($permission)->count(2);

            $this->role->permissions()->sync($permission->pluck('id'));
            expect($this->role->permissions())->count(2)
                ->and(EditTemplate::canAccess())->toBeTrue();
        }
    );
});

test('test can not access custom form')->todo();
test('test can not update/delete/create')->todo();
test('test can access custom form')->todo();
test('test can update/delete/create')->todo();

test('test can not access custom form template')->todo();
test('test can not update/delete/create template')->todo();
test('test can access custom form template')->todo();
test('test can update/delete/create template')->todo();
