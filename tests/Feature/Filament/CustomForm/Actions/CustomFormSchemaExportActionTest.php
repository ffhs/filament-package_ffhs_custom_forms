<?php

//CustomFormSchemaImportAction.php

use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Actions\CustomFormSchemaExportAction;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Resources\CustomFormResource\Pages\EditCustomForm;
use Workbench\App\FFHs\TestDynamicFormConfiguration;
use Workbench\App\Models\UserSuperAdmin;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($user);

    $this->customForm = new CustomForm([
        'short_title' => 'testForm' ,
        'custom_form_identifier' => TestDynamicFormConfiguration::identifier()
    ]);

    $this->customForm->save();

    $this->livewire = livewire(EditCustomForm::class, ['record' => $this->customForm->id]);
    $this->action = CustomFormSchemaExportAction::make();
    $this->action->livewire($this->livewire->instance());
    $this->action->record($this->customForm);
});

describe('test getCustomForm and customFrom on export action component', function () {
    it('use default record for the export', function (){
        $record = Mockery::mock(CustomForm::class)->makePartial();
        $this->action->record($record);
        expect($this->action->getCustomForm())->toBe($record);
    });

    it('use overwritten form', function (){
        $record = new CustomForm(['short_title' => '1']);
        $form = Mockery::mock(CustomForm::class)->makePartial();
        $this->action->record($record);
        $this->action->customForm($form);

        $exportForm = $this->action->getCustomForm();
        expect($this->action->getCustomForm())->toBe($form)
            ->and($exportForm)->not->toBe($record);
    });
});

test('test export action give file back', function (){
    $this->livewire->runAction('mountAction', $this->action->getName());
    $name = $this->customForm->short_title . ' - Formular '. date('Y-m-d H:i') .'.json';
    $this->livewire->assertFileDownloaded($name);
});






/*
 *

        TemporaryUploadedFile::fake('form') =>

        $this->livewire->fillForm([
            'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
            'form_file' =>
        ]);
 */

