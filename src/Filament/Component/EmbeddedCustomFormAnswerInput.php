<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRender;
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
    protected Model|int|Closure|null $variation;

    public static function make(Closure|string $relationship,string|Closure $viewMode= "default", Model|int|Closure|null $variation=null): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode,
            'relationship'=>$relationship,
            'variation'=>$variation,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(Closure|string $relationship, string|Closure $viewMode = "default",Model|int|Closure|null $variation=null)
    {
        $this->viewMode= $viewMode;
        $this->variation = $variation;
        $relationship = $this->evaluate($relationship);
        $this->relationship($relationship);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->schema(fn(EmbeddedCustomFormAnswerInput $component)=>[
            Group::make(fn($record)=> CustomFormRender::generateFormSchema($record->customForm,$component->getViewMode(),$component->getVariation())),
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
            $variation=$component->getVariation();
            if(is_null($variation))$variation = -1;
            CustomFormRender::saveHelper($answer, $data,$variation);
            return [];
        });
        $this->columns(1);


    }

    public function getViewMode(): string|Closure {
        return $this->evaluate($this->viewMode);
    }

    private function getVariation(): Model|int|null {
        if(is_null($this->variation)) return null;
        return $this->evaluate($this->variation);
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (Model $record, EmbeddedCustomFormAnswerInput $component){
            /**@var CustomFormAnswer $answer*/
            $relationshipName = $component->getRelationshipName();
            $answer = $record->$relationshipName;
            if($answer->customFieldAnswers->count() == 0) return $answer->customForm->getFormConfiguration()::displayCreateMode();
            else return $answer->customForm->getFormConfiguration()::displayEditMode();
        };
        return $this;
    }


}
