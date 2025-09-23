<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\TypeOption;

use App\Models\User;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFieldType\GenericType\CustomFieldType;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\ChildFieldRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Resources\CustomFormAnswerResource\Pages\EditCustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomField;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Tests\Feature\CustomFieldType\HasAllTypes;
use Ffhs\FilamentPackageFfhsCustomForms\TypeOption\TypeOption;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Workbench\App\FFHs\TestCustomFormConfiguration;
use Workbench\App\Models\UserSuperAdmin;
use function Pest\Livewire\livewire;

trait HasTypeOptionTestHelper
{
    protected User $user;
    protected CustomForm $customForm;
    protected CustomFormAnswer $formAnswer;
    protected ?CustomField $customField = null;

    public function schuldTest(TypeOption $searchOption, CustomFieldType $type, array $exclude = []): bool
    {
        if (in_array($type::class, $exclude, true)) {
            return true;
        }
        foreach ($type->getFlattenExtraTypeOptions() as $option) {
            if ($option::class === $searchOption::class) {
                return true;
            }
        }
        return false;
    }

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
            'custom_form_identifier' => TestCustomFormConfiguration::identifier(),
        ]);

        $this->formAnswer = CustomFormAnswer::create([
            'custom_form_id' => $this->customForm->id,
            'short_title' => 'test answer',
        ]);
    }

    public function typeOptionTestAfterEach(): void
    {
        if (!is_null($this->customField)) {
            $this->customField->delete();
        }
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
        CustomFieldType $type,
        array $extraOptions,
        array $updateOptions,
        Closure $checkNoOptionFunction,
        Closure $checkOptionFunction
    ): void {
        $displayerMock = Mockery::mock(FormFieldDisplayer::class);
        $displayerMock->shouldReceive('__invoke')
            ->zeroOrMoreTimes()
            ->andReturn(Placeholder::make('test'));

        $childRenderMock = Mockery::mock(ChildFieldRender::class);
        $childRenderMock->shouldReceive('__invoke')
            ->zeroOrMoreTimes()
            ->andReturn([]);

        $parameters = [
            'viewMode' => 'default',
            'registerComponents' => fn(array $components) => null,
            'displayer' => $displayerMock,
            'child_render' => $childRenderMock,
            'child_fields' => collect()
        ];

        $this->customField = CustomField::create([
            'name' => ['en' => 'test_field'],
            'form_position' => 1,
            'layout_end_position' => 1,
            'identifier' => uniqid(),
            'type' => $type::identifier(),
            'custom_form_id' => $this->customForm->id,
            'options' => $extraOptions,
        ]);
        $this->customForm->refresh();

        $component = $type->getFormComponent($this->customField, parameter: $parameters);
        $checkNoOptionFunction($component);

        $this->customField->update(['options' => array_merge($extraOptions, $updateOptions)]);
        Artisan::call('cache:clear');
        $this->customField->refresh();
        $this->customForm->refresh();
        $this->formAnswer->refresh();

        $component = $type->getFormComponent($this->customField, parameter: $parameters);
        $checkOptionFunction($component);
    }


//    public function simpleTestingValue(string $customFieldTypeClass, TypeOption $typeOption, array $exclude): void
//    {
//        $type = $customFieldTypeClass::make();
//
//        if (in_array($customFieldTypeClass, $exclude, true)) {
//            expect(true)->toBeTrue();
//            return;
//        }
//
//        $attribut = null;
//        foreach ($type->getFlattenExtraTypeOptions() as $optionAttribut => $option) {
//            if ($option::class === $typeOption::class) {
//                $attribut = $optionAttribut;
//                break;
//            }
//        }
//
//        if (!$attribut) {
//            expect(true)->toBeTrue();
//            return;
//        }
//
//        $defaultColumns = $type->getFlattenExtraTypeOptions()[$attribut]->getDefaultValue() ?? 1;
//        $columnSpan = 1;
//
//        $checkNoOptionFunction = function (Component $component) use ($defaultColumns) {
//            expect($component)->not()->toBeNull()
//                ->and($component->getColumnSpan()['default'])->toBe($defaultColumns);
//        };
//
//        $checkOptionFunction = function (Component $component) use ($columnSpan) {
//            expect($component)->not()->toBeNull()
//                ->and($component->getColumnSpan()['default'])->toBe($columnSpan);
//        };
//
//        $this->componentTestField(
//            $customFieldType,
//            $extraOptions,
//            ['column_span' => $columnSpan],
//            $checkNoOptionFunction,
//            $checkOptionFunction
//        );
//    }
}
