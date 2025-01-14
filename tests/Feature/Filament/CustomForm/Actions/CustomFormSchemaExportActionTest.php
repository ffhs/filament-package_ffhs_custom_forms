<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\ListCustomForm;
use Workbench\App\Models\UserSuperAdmin;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($user);

    $this->livewire = livewire(ListCustomForm::class);
    $this->action = CustomFormSchemaExportAction::make();
});

it("test export action")->todo();


/*
 *

        TemporaryUploadedFile::fake('form') =>

        $this->livewire->fillForm([
            'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
            'form_file' =>
        ]);
 */

