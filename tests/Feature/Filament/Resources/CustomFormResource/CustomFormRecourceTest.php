<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DefaultFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\ListCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\EditCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Livewire\Features\SupportTesting\Testable;
use Workbench\App\Models\UserSuperAdmin;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = UserSuperAdmin::create([
        'name' => 'tester',
        'email' => 'testing@test.com',
        'password' => '1234'
    ]);
    $this->actingAs($user);
});

describe('Can access resource', function () {
    it('can create custom form', function ($shortTitle, $identifier) {

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(CreateCustomForm::class);

        $livewire->fillForm([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
        ]);

        $livewire->call('create');
        $livewire->assertHasNoFormErrors();

        expect(CustomForm::count())->toBe(1);
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier()],
        ['Test Formular 2', DefaultFormConfiguration::identifier()],
    ]);

    it('can edit custom form', function ($shortTitle, $identifier) {

        $customForm = CustomForm::create([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(EditCustomForm::class, ['record' => $customForm->id]);

        $livewire->call('save');
        $livewire->assertHasNoFormErrors();
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier()],
        ['Test Formular 2', DefaultFormConfiguration::identifier()],
    ]);

    it('can list custom form', function ($shortTitle, $identifier) {

        $customForm = CustomForm::create([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        expect(function () use ($customForm) {
            livewire(ListCustomFormAnswer::class);
        })->not->toThrow(Error::class);

    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier()],
        ['Test Formular 2', DefaultFormConfiguration::identifier()],
    ]);
})->only();
