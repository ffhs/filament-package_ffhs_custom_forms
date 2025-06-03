<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Exceptions\ModelIsNoCustomAnswerException;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\CanLoadFormAnswerer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseAutosaveCustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UsePosSplit;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;

class EmbeddedCustomForm extends Field implements CanEntangleWithSingularRelationships
{
    protected string $view = 'filament-package_ffhs_custom_forms::filament.components.embedded-custom-form';

    use EntanglesStateWithSingularRelationship;
    use HasViewMode;
    use UseAutosaveCustomForm;
    use CanLoadFormAnswerer;
    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;

    public function getRecord(): null|Model|CustomFormAnswer
    {
        $record = parent::getRecord();
        if ($record instanceof CustomFormAnswer) {
            return $record;
        }
        throw new ModelIsNoCustomAnswerException();
    }

    public function getChildComponents(): array
    {
        return once(function () {
            return CustomFormRender::generateFormSchema($this->getRecord()->customForm, $this->getViewMode());
        });
    }

    public function mutateRelationshipDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        if ($this->isUseLayoutTypeSplit()) {
            $data = $this->loadLayoutTypeSplitAnswerData($record);
        } elseif ($this->isUseFieldSplit()) {
            $data = $this->loadFieldTypeSplitAnswerData($record);
        } elseif ($this->isUsePoseSplit()) {
            $data = $this->loadPosTypeSplitAnswerData($record);
        } else {
            $data = $this->loadCustomAnswerData($record);
        }

        if ($this->mutateRelationshipDataBeforeFillUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeFillUsing, ['data' => $data,]);
        }

        return $data;
    }

    protected function setUp(): void
    {
        $this->autoViewMode();
        $this->label("");

        $this->columns(1);

//        $this->setupFormSaving();
//        $this->setUpAutoSaving();


        //SetUp Auto Update
//        $this->afterStateUpdated(function (CustomFormComponent $component, array $state, ?CustomFormAnswer $record) {
//            if (!$component->getIsAutoSave()) {
//                return;
//            }
//            CustomForms::save($record, $component->getLivewire()->getForm('form'));
//        });

    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'viewMode' => [$this->getViewMode()],
            'customForm' => [$this->getRecord()->customForm],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName)
        };
    }

//
//    public function getChildComponents(): array
//    {
//        $record = $this->getCachedExistingRecord() ?? $this->getRecord();
//        if (is_null($record)) {
//            return [];
//        }
//
//
//        if (!is_array($this->childComponents) || empty($this->childComponents)) {
//            if ($this->isUseLayoutTypeSplit()) {
//                $schema = $this->getLayoutTypeSplitFormSchema($this);
//            } //Field Splitting
//            else {
//                if ($this->isUseFieldSplit()) {
//                    $schema = $this->getFieldSplitFormSchema($this);
//                } //Position Splitting
//                else {
//                    if ($this->isUsePoseSplit()) {
//                        $schema = $this->getPosSplitFormSchema($this);
//                    } //Default
//                    else {
//                        $schema = $this->getDefaultFormSchema($this);
//                    }
//                }
//            }
//
//            $this->childComponents = $schema;
//        }
//
//        return $this->childComponents;
//    }
//
//    public function getIsAutoSave(): bool
//    {
//        return $this->evaluate($this->isAutoSave);
//    }
//

//    protected function setUpFormLoading(): void
//    {
//
//        $this->mutateRelationshipDataBeforeFillUsing(function (
//            array $data,
//            Model $record,
//            EmbeddedCustomForm $component
//        ) {
//
//            /**@var CustomFormAnswer $answer */
//            $relationshipName = $component->getRelationshipName();
//            $answer = $record->$relationshipName;
//
//
//            if ($this->isUseLayoutTypeSplit()) {
//                $output = $this->loadLayoutTypeSplitAnswerData($answer);
//            } else {
//                if ($this->isUseFieldSplit()) {
//                    $output = $this->loadFieldTypeSplitAnswerData($answer);
//                } else {
//                    if ($this->isUsePoseSplit()) {
//                        $output = $this->loadPosTypeSplitAnswerData($answer);
//                    } else {
//                        $output = CustomFormLoadHelper::load($answer);
//                    }
//                }
//            }
//
//
//            return $output;
//        });
//    }
//
//    protected function setupFormSaving(): void
//    {
//        $this->mutateRelationshipDataBeforeSaveUsing(function (
//            Model $record,
//            EmbeddedCustomForm $component
//        ) {
//            /**@var CustomFormAnswer $answer */
//            if ($component->getIsAutoSave()) {
//                return [];
//            }
//            $this->saveForm($component, $record);
//            return [];
//        });
//    }
//
//    /**
//     * @param EmbeddedCustomForm $component
//     * @param Model $record
//     * @return void
//     */
//    protected function saveForm(EmbeddedCustomForm $component, Model $record): void
//    {
//        $relationshipName = $component->getRelationshipName();
//        $answer = $record->$relationshipName;
//        $formDataPath = $component->getStatePath(false);
//
//        CustomForms::save($answer, $component->getLivewire()->getForm('form'), path: $formDataPath);
//    }
//
//    protected function setUpAutoSaving(): void
//    {
//        $this->afterStateUpdated(function (EmbeddedCustomForm $component, array $state, ?Model $record) {
//            if (!$component->getIsAutoSave()) {
//                return;
//            }
//            /**@var CustomFormAnswer $answer */
//            $this->saveForm($component, $record);
//        });
//    }
//
//    private function getLayoutTypeSplitFormSchema(EmbeddedCustomForm $component): array
//    {
//        $record = $this->getCachedExistingRecord() ?? $this->getRecord();
//        return SplitCustomFormRender::renderFormLayoutType(
//            $component->getLayoutTypeSplit(),
//            $record->customForm,
//            $component->getViewMode()
//        );
//    }
//
//    private function getFieldSplitFormSchema(EmbeddedCustomForm $component): array
//    {
//        return SplitCustomFormRender::renderFormFromField(
//            $component->getFieldSplit(),
//            $component->getViewMode()
//        );
//    }
//
//    private function getPosSplitFormSchema(EmbeddedCustomForm $component): array
//    {
//        $record = $this->getCachedExistingRecord() ?? $this->getRecord();
//
//        [$beginPos, $endPos] = $this->getPoseSpilt();
//        return
//            SplitCustomFormRender::renderFormPose(
//                $beginPos,
//                $endPos,
//                CustomForm::cached($record->custom_form_id),
//                $component->getViewMode()
//            );
//    }
//
//    private function getDefaultFormSchema(EmbeddedCustomForm $component): array
//    {
//        $record = $this->getCachedExistingRecord() ?? $this->getRecord();
//        return CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id),
//            $component->getViewMode());
//    }
//
//    // f. Filament
////    public function getCachedExistingRecord(): ?Model
////    {
////        if ($this->cachedExistingRecord) return $this->cachedExistingRecord;
////
////        $parentRecord = $this->getRecord();
////        if( is_null($parentRecord)) return null;
////        $record = Cache::remember($parentRecord::class. "-". $parentRecord->id . "-customFormAnswerCachedModel-" . $this->relationship,
////            config('ffhs_custom_forms.cache_duration'),
////            fn() => $this->getRelationship()?->getResults()
////        );
////
////        if (! $record?->exists) return null;
////
////        return $this->cachedExistingRecord = $record;
////    }
}
