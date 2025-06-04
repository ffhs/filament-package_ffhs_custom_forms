<?php

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormHeaderActions\CustomFormSchemaImportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\ListCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Workbench\App\FFHs\TestCustomFormConfiguration;
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
    $this->action = CustomFormSchemaImportAction::make();
});

test('open action modal from import', function () {
    $this->action->livewire($this->livewire->instance());
    $this->livewire->call('mountAction', $this->action->getName());

    $this->livewire->assertSee('Formularart');
    $this->livewire->assertSee('Formulardatei');
});

describe('getFormSchema visible options after file upload and select of form config', function () {
    test('import without form', function () {
        $this->action->livewire($this->livewire->instance());
        $this->livewire->call('mountAction', $this->action->getName());

        $this->livewire->assertSee('Formularart');
        $this->livewire->assertSee('Formulardatei');
    });

    test('import with form', function () {
        $customFormExisting = new CustomForm([
            'custom_form_identifier' => TestCustomFormConfiguration::identifier(),
            'short_title' => 'test'
        ]);
        $customFormExisting->save();

        $this->livewire = livewire(EditCustomForm::class, ['record' => $customFormExisting->id]);#
        $this->action->record($customFormExisting);
        $this->action->livewire($this->livewire->instance());
        $this->livewire->call('mountAction', $this->action->getName());

        $this->livewire->assertDontSee('Formularart');
        $this->livewire->assertSee('Formulardatei');
    });
});

test('auto fill form informations', function () {
})->todo('implement');
test('auto on implements template disable is template option', function () {
})->todo('implement');
test('on import the form in an form, does it apply on the form', function () {
})->todo('implement');

/*
 *

        TemporaryUploadedFile::fake('form') =>

        $this->livewire->fillForm([
            'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
            'form_file' =>
        ]);
 */
