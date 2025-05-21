<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomForms\TypeOptions;

use App\Models\User;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomField\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Illuminate\Support\Facades\Artisan;
use Workbench\App\FFHs\TestDynamicFormConfiguration;
use Workbench\App\Models\UserSuperAdmin;

use function Pest\Livewire\livewire;

trait HasTypeOptionEasyTest
{
    protected User $user;
    protected CustomForm $customForm;
    protected CustomFormAnswer $formAnswer;
    protected ?CustomField $customField = null;

    public function typeOptionTestBeforeEach(): void
    {
        $this->user = UserSuperAdmin::create([
            'name' => 'tester',
            'email' => 'testing@test.com',
            'password' => '1234',
        ]);
        $this->actingAs($this->user);

        /**@var CustomForm $customForm */
        $this->customForm = CustomForm::create([
            'short_title' => 'My custom form title',
            'custom_form_identifier' => TestDynamicFormConfiguration::identifier(),
        ]);

        $this->formAnswer = CustomFormAnswer::create([
            'custom_form_id' => $this->customForm->id,
            'short_title' => 'test answare',
        ]);
    }

    public function typeOptionTestAfterEach(): void
    {
        if (!is_null($this->customField)) $this->customField->delete();
        $this->customForm->refresh();
        $this->formAnswer->refresh();
    }


    public function livewireTestField(
        string $customFieldIdentifier,
        array $extraOptions,
        array $updateOptions,
        Closure $checkNoOptionFunction,
        Closure $checkOptionFunction
    ): void {
        $this->customField = CustomField::create([
            'name' => ['en' => 'test_field'],
            'form_position' => 1,
            'layout_end_position' => 1,
            'identifier' => uniqid(),
            'type' => $customFieldIdentifier,
            'custom_form_id' => $this->customForm->id,
            'options' => $extraOptions,
        ]);
        $this->customForm->refresh();


        $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnswer->id]);
        $livewire->assertSuccessful();
        $checkNoOptionFunction($livewire);

        $this->customField->update(['options' => array_merge($extraOptions, $updateOptions)]);
        Artisan::call('cache:clear');
        $this->customField->refresh();
        $this->customForm->refresh();
        $this->formAnswer->refresh();

        $livewire = livewire(EditCustomFormAnswer::class, ['record' => $this->formAnswer->id]);
        $livewire->assertSuccessful();
        $checkOptionFunction($livewire);
    }


    public function componentTestField(
        string $customFieldIdentifier,
        array $extraOptions,
        array $updateOptions,
        Closure $checkNoOptionFunction,
        Closure $checkOptionFunction
    ): void {
        $type = CustomFieldType::getTypeFromIdentifier($customFieldIdentifier);
        $this->customField = CustomField::create([
            'name' => ['en' => 'test_field'],
            'form_position' => 1,
            'layout_end_position' => 1,
            'identifier' => uniqid(),
            'type' => $customFieldIdentifier,
            'custom_form_id' => $this->customForm->id,
            'options' => $extraOptions,
        ]);
        $this->customForm->refresh();

        $component = $type->getFormComponent(
            $this->customField,
            $this->customForm,
            'default',
            ['renderer' => fn() => []]
        );
        $checkNoOptionFunction($component);

        $this->customField->update(['options' => array_merge($extraOptions, $updateOptions)]);
        Artisan::call('cache:clear');
        $this->customField->refresh();
        $this->customForm->refresh();
        $this->formAnswer->refresh();

        $component = $type->getFormComponent(
            $this->customField,
            $this->customForm,
            'default',
            ['renderer' => fn() => []]
        );
        $checkOptionFunction($component);
    }

}
