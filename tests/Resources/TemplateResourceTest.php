<?php

use Ffhs\FilamentPackageFfhsCustomForms\CustomForm\FormConfiguration\DefaultFormConfiguration;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormResource\Pages\CreateCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\CreateTemplate;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\TemplateResource\Pages\EditTemplate;
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

describe('Can access template resource', function () {
    it('can create template', function ($shortTitle, $identifier, $templateIdentifier) {

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(CreateTemplate::class);

        $livewire->fillForm([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
            'template_identifier' => $templateIdentifier,
        ]);

        $livewire->call('create');
        $livewire->assertHasNoFormErrors();

        expect(CustomForm::count())->toBe(1);
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier(), 'test_template'],
        ['Test Formular 2', DefaultFormConfiguration::identifier(), 'test_template2'],
    ]);

    it('can\'t create template without template_identifier', function ($shortTitle, $identifier) {
        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(CreateTemplate::class);

        $livewire->fillForm([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
        ]);

        $livewire->call('create');
        $livewire->assertHasFormErrors();

        expect(CustomForm::count())->toBe(0);
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier()],
        ['Test Formular 2', DefaultFormConfiguration::identifier()],
    ]);

    it('can\'t create template when identifier is exist', function ($shortTitle, $identifier, $templateIdentifier) {
        CustomForm::create([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
            'template_identifier' => $templateIdentifier
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(CreateTemplate::class);

        $livewire->fillForm([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
            'template_identifier' => $templateIdentifier,
        ]);

        $livewire->call('create');
        $livewire->assertHasFormErrors();

        expect(CustomForm::count())->toBe(1);
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier(), 'test_template'],
        ['Test Formular 2', DefaultFormConfiguration::identifier(), 'test_template2'],
    ]);

    it('can edit template', function ($shortTitle, $identifier, $templateIdentifier) {
        $template = CustomForm::create([
            'short_title' => $shortTitle,
            'custom_form_identifier' => $identifier,
            'template_identifier' => $templateIdentifier
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(EditTemplate::class, ['record' => $template->id]);;

        $livewire->call('save');
        $livewire->assertHasNoFormErrors();

        expect(CustomForm::count())->toBe(1);
    })->with([
        ['Test Formular 1', DefaultFormConfiguration::identifier(), 'test_template'],
        ['Test Formular 2', DefaultFormConfiguration::identifier(), 'test_template2'],
    ]);

    it('no template select component in edit template', function () {
        $template = CustomForm::create([
            'short_title' => 'Test Formular',
            'custom_form_identifier' => DefaultFormConfiguration::identifier(),
            'template_identifier' => 'test_template'
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(EditTemplate::class, ['record' => $template->id]);;

        $livewire->assertDontSee('<span class="fi-fo-field-label-content">Templates</span>');
    });

    it('list templates', function () {
        $template = CustomForm::create([
            'short_title' => 'Test Formular',
            'custom_form_identifier' => DefaultFormConfiguration::identifier(),
            'template_identifier' => 'test_template'
        ]);

        /** @var CreateCustomForm|Testable $livewire */
        $livewire = livewire(EditTemplate::class, ['record' => $template->id]);

        $livewire->assertDontSee('<span class="fi-fo-field-label-content">Templates</span>');
    });
});
