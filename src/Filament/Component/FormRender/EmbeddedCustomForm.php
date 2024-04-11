<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\FormRender;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UsePosSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;

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

        $this->setupFormSchema();

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
            $form = CustomForm::cached($answer->custom_form_id);
            if($answer->customFieldAnswers->count() == 0) return $form->getFormConfiguration()::displayCreateMode();
            else{
                $form = CustomForm::cached($answer->custom_form_id);
                return $form->getFormConfiguration()::displayEditMode();
            }
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

    private function setupFormSchema(): void {
        //Type Splitting
        if ($this->isUseLayoutTypeSplit()) $this->setLayoutTypeSplitFormSchema();
        //Field Splitting
        else if ($this->isUseFieldSplit()) $this->setFieldSplitFormSchema();
        //Position Splitting
        else if ($this->isUsePoseSplit()) $this->setPosSplitFormSchema();
        //Default
        else $this->setDefaultFormSchema();

        $this->columns(1);
    }

    /**
     * @return void
     */
    private function setLayoutTypeSplitFormSchema(): void {
        $this->schema(fn(EmbeddedCustomForm $component) => [
            Group::make()->schema(fn(CustomFormAnswer|null $record) => is_null($record) ? [] :
                SplitCustomFormRender::renderFormLayoutType(
                    $component->getLayoutTypeSplit(),
                    CustomForm::cached($record->custom_form_id),
                    $component->getViewMode()
                )
            ),
        ]);
    }

    /**
     * @return void
     */
    private function setFieldSplitFormSchema(): void {
        $this->schema(fn(EmbeddedCustomForm $component) => [
            Group::make()->schema(fn(CustomFormAnswer|null $record) => is_null($record) ? [] :
                SplitCustomFormRender::renderFormFromField(
                    $component->getFieldSplit(),
                    $component->getViewMode()
                )
            ),
        ]);
    }

    /**
     * @return void
     */
    private function setPosSplitFormSchema(): void {
        $this->schema(fn(EmbeddedCustomForm $component) => [
            Group::make()->schema(function (CustomFormAnswer|null $record) use ($component) {
                if (is_null($record)) return [];

                [$beginPos, $endPos] = $this->getPoseSpilt();

                return SplitCustomFormRender::renderFormPose(
                    $beginPos,
                    $endPos,
                    CustomForm::cached($record->custom_form_id),
                    $component->getViewMode()
                );
            }),
        ]);
    }

    /**
     * @param  EmbeddedCustomForm  $component
     * @param  Model  $record
     * @param  array  $data
     * @return void
     */
    function saveForm(EmbeddedCustomForm $component, Model $record, array $data): void {
        $relationshipName = $component->getRelationshipName();
        $answer = $record->$relationshipName;
        CustomFormRender::saveHelper($answer, $data);
    }


    /**
     * @return void
     */
    private function setDefaultFormSchema(): void {
        $this->schema(fn(EmbeddedCustomForm $component) => [
            Group::make(fn(CustomFormAnswer $record) => CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id),
                $component->getViewMode())),
        ]);
    }

    /**
     * @return void
     */
    private function setUpFormLoading(): void {
        $this->mutateRelationshipDataBeforeFillUsing(function (array $data, Model $record,
            EmbeddedCustomForm $component) {
            /**@var CustomFormAnswer $answer */
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            return CustomFormRender::loadHelper($answer);
        });
    }

    /**
     * @return void
     */
    private function setupFormSaving(): void {
        $this->mutateRelationshipDataBeforeSaveUsing(function (array $data, Model $record,
            EmbeddedCustomForm $component) {
            /**@var CustomFormAnswer $answer */
            $this->saveForm($component, $record, $data);
            return [];
        });
    }

    /**
     * @return void
     */
    private function setUpAutoSaving(): void {
        $this->afterStateUpdated(function (EmbeddedCustomForm $component, array $state, ?Model $record) {
            if (!$component->getIsAutoSave()) return;
            /**@var CustomFormAnswer $answer */
            $this->saveForm($component, $record, $state);
        });
    }

}
