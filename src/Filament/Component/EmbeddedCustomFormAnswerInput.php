<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Forms\Components\Group;
use Illuminate\Database\Eloquent\Model;

class EmbeddedCustomFormAnswerInput extends Component implements CanEntangleWithSingularRelationships
{

    use EntanglesStateWithSingularRelationship;


    protected string $view = 'filament-forms::components.group';
    protected string|Closure $viewMode;
    protected bool|Closure $isAutoSave;

    public static function make(Closure|string $relationship,string|Closure $viewMode= "default"): static
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
        $this->schema(fn(EmbeddedCustomFormAnswerInput $component)=>[
            Group::make(fn(CustomFormAnswer $record)=> CustomFormRender::generateFormSchema(CustomForm::cached($record->custom_form_id),$component->getViewMode())),
        ]);
        $this->mutateRelationshipDataBeforeFillUsing(function(array $data, Model $record, EmbeddedCustomFormAnswerInput $component){
            /**@var CustomFormAnswer $answer*/
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            return CustomFormRender::loadHelper($answer);
        });
        $this->mutateRelationshipDataBeforeSaveUsing(function(array $data, Model $record, EmbeddedCustomFormAnswerInput $component){
            /**@var CustomFormAnswer $answer*/
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            CustomFormRender::saveHelper($answer, $data);
            return [];
        });
        $this->columns(1);

        //SetUp Auto Update
        $this->afterStateUpdated(function (EmbeddedCustomFormAnswerInput $component, array $state,?Model $record){
            if(!$component->getIsAutoSave()) return;
            /**@var CustomFormAnswer $answer*/
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            CustomFormRender::saveHelper($answer, $state);
        });

    }

    public function getViewMode(): string|Closure {
        return $this->evaluate($this->viewMode);
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (Model $record, EmbeddedCustomFormAnswerInput $component){
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


}
