<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRenderForm;
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
        if(!is_null($relationship)) $this->relationship($relationship);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->schema(fn(EmbeddedCustomFormAnswerInput $component)=>[
            Group::make(fn($record)=> CustomFormRenderForm::generateFormSchema($record->customForm,$component->getViewMode(),$component->getVariation()))
        ]);
        $this->mutateRelationshipDataBeforeFillUsing(function($data,EmbeddedCustomFormAnswerInput $component){
            /**@var CustomFormAnswer $record*/
            $record = $component->getRelationship()->getRelated();
            return CustomFormRenderForm::loadHelper($record);
        });
        $this->mutateRelationshipDataBeforeSaveUsing(function($data,EmbeddedCustomFormAnswerInput $component){
            /**@var CustomFormAnswer $record*/
            $record = $component->getRelationship()->getRelated();
            CustomFormRenderForm::saveHelper($record, $data,$component->getVariation());
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


}
