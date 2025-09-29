<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomFormAnswer\Render\FormFieldDisplayer;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanSaveFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasCustomFormData;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasEmbeddedCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseAutosaveCustomForm;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;

class CustomFormAnswerEditor extends Field implements CanEntangleWithSingularRelationships
{
    use HasEmbeddedCustomForm {
        HasEmbeddedCustomForm::relationship as embeddedRelationship;
    }
    use UseAutosaveCustomForm;
    use CanSaveFormAnswer;
    use HasCustomFormData;

    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.custom-form-answer-editor';

    public function relationship(
        string $name,
        bool|Closure $condition = true,
        Closure|string|null $relatedModel = null
    ): static {
        return $this->embeddedRelationship($name, $condition, $relatedModel)
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

        $data = $this->mutateRelationshipDataBeforeSave($state);

        $this->saveFormAnswer($customFormAnswer, $this->getChildSchema());

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
