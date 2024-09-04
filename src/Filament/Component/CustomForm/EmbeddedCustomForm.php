<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;

use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormSaveHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UsePosSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EmbeddedCustomForm extends Component implements CanEntangleWithSingularRelationships
{

    use EntanglesStateWithSingularRelationship;

    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;
    use UseViewMode;

    protected string $view = 'filament-forms::components.group';
    protected bool|Closure $isAutoSave;


    public static function make(Closure|string $relationship, string|Closure $viewMode= "default"): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode,
            'relationship'=>$relationship,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(Closure|string $relationship, string|Closure $viewMode = "default")
    {
        $this->viewMode= $viewMode;
        $this->isAutoSave=false;
        $relationship = $this->evaluate($relationship);
        $this->relationship($relationship);
    }

    protected function setUp(): void {
        parent::setUp();

        $this->label("");

        $this->columns(1);

        $this->setUpFormLoading();

        $this->setupFormSaving();

        $this->setUpAutoSaving();

    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (Model $record, EmbeddedCustomForm $component){
            /**@var CustomFormAnswer $answer*/
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            $form = $answer->customForm;
            if($answer->cachedAnswers()->count() == 0) return $form->getFormConfiguration()::displayCreateMode();
            else return $form->getFormConfiguration()::displayEditMode();

        };
        return $this;
    }

    public function getIsAutoSave():bool {
        return $this->evaluate($this->isAutoSave);
    }

    public function autoSave(bool|Closure $isAutoSave = true):static {
        $this->isAutoSave = $isAutoSave;
        return $this;
    }

    public function getChildComponents(): array
    {
        $record = $this->getCachedExistingRecord()?? $this->getRecord();
        if(is_null($record)) return [];


        if(!is_array($this->childComponents) || empty($this->childComponents)){
            if ($this->isUseLayoutTypeSplit()) $schema = $this->getLayoutTypeSplitFormSchema($this);
            //Field Splitting
            else if ($this->isUseFieldSplit()) $schema = $this->getFieldSplitFormSchema($this);
            //Position Splitting
            else if ($this->isUsePoseSplit()) $schema = $this->getPosSplitFormSchema($this);
            //Default
            else $schema = $this->getDefaultFormSchema($this);

            $this->childComponents = $schema;
        }

        return $this->childComponents;
    }

    private function getLayoutTypeSplitFormSchema(EmbeddedCustomForm $component): array {
        $record = $this->getCachedExistingRecord()?? $this->getRecord();
        return SplitCustomFormRender::renderFormLayoutType(
                $component->getLayoutTypeSplit(),
                $record->customForm,
                $component->getViewMode()
            );
    }

    private function getFieldSplitFormSchema(EmbeddedCustomForm $component): array {
        return SplitCustomFormRender::renderFormFromField(
                $component->getFieldSplit(),
                $component->getViewMode()
            );
    }

    private function getPosSplitFormSchema(EmbeddedCustomForm $component): array {
        $record = $this->getCachedExistingRecord()?? $this->getRecord();

        [$beginPos, $endPos] = $this->getPoseSpilt();
        return
            SplitCustomFormRender::renderFormPose(
                $beginPos,
                $endPos,
                CustomForm::cached($record->custom_form_id),
                $component->getViewMode()
            );
    }


    private function getDefaultFormSchema(EmbeddedCustomForm $component): array {
        $record = $this->getCachedExistingRecord()?? $this->getRecord();
        return CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id), $component->getViewMode());
    }

    /**
     * @param  EmbeddedCustomForm  $component
     * @param  Model  $record
     * @return void
     */
    function saveForm(EmbeddedCustomForm $component, Model $record): void {
        $relationshipName = $component->getRelationshipName();
        $answer = $record->$relationshipName;
        $formDataPath = $component->getStatePath(false);

        CustomFormSaveHelper::save($answer,  $component->getLivewire()->getForm('form'), path: $formDataPath);
    }


    private function setUpFormLoading(): void {

        $this->mutateRelationshipDataBeforeFillUsing(function (array $data, Model $record, EmbeddedCustomForm $component) {

            /**@var CustomFormAnswer $answer */
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;


            if ($this->isUseLayoutTypeSplit()) $output = $this->loadLayoutTypeSplitAnswerData($answer);
            else if ($this->isUseFieldSplit()) $output = $this->loadFieldTypeSplitAnswerData($answer);
            else if ($this->isUsePoseSplit()) $output = $this->loadPosTypeSplitAnswerData($answer);
            else $output = CustomFormLoadHelper::load($answer);


            return $output;
        });
    }

    private function setupFormSaving(): void {
        $this->mutateRelationshipDataBeforeSaveUsing(function ( Model $record,
            EmbeddedCustomForm $component) {
            /**@var CustomFormAnswer $answer */
            if($component->getIsAutoSave()) return [];
            $this->saveForm($component, $record);
            return [];
        });
    }


    private function setUpAutoSaving(): void {
        $this->afterStateUpdated(function (EmbeddedCustomForm $component, array $state, ?Model $record) {
            if (!$component->getIsAutoSave()) return;
            /**@var CustomFormAnswer $answer */
            $this->saveForm($component, $record);
        });
    }

    // f. Filament
    public function getCachedExistingRecord(): ?Model
    {
        if ($this->cachedExistingRecord) return $this->cachedExistingRecord;

        $parentRecord = $this->getRecord();
        if( is_null($parentRecord)) return null;
        $record = Cache::remember($parentRecord::class. "-". $parentRecord->id . "-customFormAnswerCachedModel-" . $this->relationship,
            config('ffhs_custom_forms.cache_duration'),
            fn() => $this->getRelationship()?->getResults()
        );

        if (! $record?->exists) return null;

        return $this->cachedExistingRecord = $record;
    }
}
