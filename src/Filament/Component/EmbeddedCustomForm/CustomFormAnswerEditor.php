<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseAutosaveCustomForm;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;

class CustomFormAnswerEditor extends Field implements CanEntangleWithSingularRelationships
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-answer-editor';

    use HasEmbeddedCustomForm {
        HasEmbeddedCustomForm::fillFromRelationship insteadof EntanglesStateWithSingularRelationship;
    }
    use EntanglesStateWithSingularRelationship {
        EntanglesStateWithSingularRelationship::relationship as parentRelationship;
    }
    use UseAutosaveCustomForm;
    use CanSaveFormAnswer;
    use HasCustomFormData;

    public function relationship(
        string $name,
        bool|Closure $condition = true,
        Closure|string|null $relatedModel = null
    ): static {
        return $this->parentRelationship($name, $condition, $relatedModel)
            ->customForm(fn() => $this->getCachedExistingRecord()?->customForm)
            ->saveRelationshipsUsing(function () {
                $data = $this->getChildSchema()?->getState(shouldCallHooksBefore: false);
                $this->saveCustomFormAnswerRelation($data);
            });
    }

    protected function saveCustomFormAnswerRelation(array $state): void
    {
        /**@var CustomFormAnswer $customFormAnswer */
        $customFormAnswer = $this->getCachedExistingRecord();

        if (!$customFormAnswer) {
            return;
        }

        $customFormAnswer->save();
        $data = $this->mutateRelationshipDataBeforeSave($state);

        $this->saveFormAnswer($customFormAnswer, $this->getLivewire()?->form, $this->getStatePath());

        $customFormAnswer
            ->fill($data)
            ->save();
    }

    protected function setUp(): void
    {
        $this
            ->fieldDisplayer(FormFieldDisplayer::make(...))
            ->afterStateUpdated($this->runAutoSave(...))
            ->live(condition: $this->isAutoSaving(...))
            ->schema($this->getCustomFormSchema(...))
            ->columns(1)
            ->autoViewMode()
            ->hiddenLabel();
    }
}
