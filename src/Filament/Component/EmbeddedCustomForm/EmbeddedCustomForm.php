<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasFallbackCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseAutosaveCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseParentFilamentForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseSplitCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseSplitFormSchema;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Field;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;

class EmbeddedCustomForm extends Field implements CanEntangleWithSingularRelationships
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.embedded-custom-form';

    use EntanglesStateWithSingularRelationship;
    use UseSplitCustomForm;
    use UseAutosaveCustomForm;
    use CanLoadFormAnswer;
    use CanSaveFormAnswer;
    use HasFallbackCustomForm;
    use HasViewMode;
    use UseParentFilamentForm;
    use UseSplitFormSchema;

    public function getChildComponents(): array
    {
        return once(function () {
            return $this->getFormSchema($this);
        });
    }

    public function fillFromRelationship(): void
    {
        $data = $this->loadAnswerData($this);
        $data = $this->mutateRelationshipDataBeforeFill($data);
        $this->getChildComponentContainer()->fill($data, andCallHydrationHooks: false, andFillStateWithNull: false);
    }

    public function getCustomFormAnswer(): null|Model|CustomFormAnswer
    {
        if (is_null($this->getRelationshipName())) {
            return $this->getRecord();
        }
        $customFormAnswer = $this->getCachedExistingRecord();
        if (!is_null($customFormAnswer)) {
            return $customFormAnswer;
        }

        return once(function () {
            //ToDo Test
            $customFormFallback = $this->getFallbackCustomForm();
            if (is_null($customFormFallback)) {
                return null;
            }

            /**@var CustomFormAnswer $formAnswer */
            $formAnswer = app(CustomFormAnswer::class);
            $formAnswer->fill([
                'custom_form_id' => $customFormFallback->id,
                'short_title' => $this->getFallbackName(),
            ]);
            $formAnswer->setRelation('customForm', $customFormFallback);
            $formAnswer->setRelation('customFieldAnswers', collect());

            return $formAnswer;
        });
    }

    public function getCustomForm(): ?CustomForm
    {
        return $this->getCustomFormAnswer()?->customForm;
    }

    public function runAutoSave(EmbeddedCustomForm $component, HasForms $livewire, array $state): void
    {
        $customFormAnswer = $component->getCustomFormAnswer();
        if (is_null($customFormAnswer) || !$component->isAutoSaving()) {
            return;
        }

        if ($component->getRelationshipName()) {
            $component->saveCustomFormAnswerRelation($component, $livewire, $state);
            return;
        }

        $form = $component->getFilamentForm($component, $livewire);
        $state = $component->getState();

        if (is_null($form)) {
            return;
        }

        $this->saveFormAnswer(
            $customFormAnswer,
            $form,
            $state,
            $component->getStatePath()
        );
    }

    protected function setUp(): void
    {
        $this->autoViewMode();
        $this->label('');
        $this->columns(1);

        //AutoSave
        $this->live(condition: $this->isAutoSaving(...));
        $this->afterStateUpdated($this->runAutoSave(...));

        //RelationSave
        $this->saveRelationshipsUsing(function (EmbeddedCustomForm $component, HasForms $livewire) {
            $data = $component
                ->getChildComponentContainer()
                ->getState(shouldCallHooksBefore: false);
            $this->saveCustomFormAnswerRelation($component, $livewire, $data);
        });
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'viewMode' => [$this->getViewMode()],
            'customForm' => [$this->getCustomForm()],
            'customFormAnswer' => [$this->getCustomFormAnswer()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName)
        };
    }

    protected function saveCustomFormAnswerRelation(
        EmbeddedCustomForm $component,
        HasForms $livewire,
        array $state,
    ): void {
        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $component->getCachedExistingRecord();

        if (!$customFormAnswer) {
            $customFormAnswer = $this->getCustomFormAnswer();
            if (is_null($customFormAnswer)) {
                return;
            }
            $customFormAnswer->save();
        }

        $form = $component->getFilamentForm($component, $livewire);
        $data = $component->mutateRelationshipDataBeforeSave($state);
        $this->saveFormAnswer($customFormAnswer, $form, $data, $component->getStatePath());

        $customFormAnswer->fill($data)->save();
    }
}
