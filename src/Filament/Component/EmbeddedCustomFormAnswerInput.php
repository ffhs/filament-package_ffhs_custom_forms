<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Form\CustomFormRenderForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Database\Eloquent\Model;

class EmbeddedCustomFormAnswerInput extends Component implements CanEntangleWithSingularRelationships
{

    use EntanglesStateWithSingularRelationship;


    protected string $view = 'filament-forms::components.group';
    protected string|Closure $viewMode;
    protected Model|int|Closure|null $variation;

    public static function make(Closure|null $relationship,string|Closure $viewMode= "default", Model|int|Closure|null $variation=null): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode,
            'relationship'=>$relationship,
            'variation'=>$variation,
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(Closure|null $relationship, string|Closure $viewMode = "default",Model|int|Closure|null $variation=null)
    {
        $this->viewMode=
        $relationship = $this->evaluate($relationship);
        if(!is_null($relationship)) $this->relationship($relationship);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        /**@var CustomFormAnswer $record*/
        $record = $this->getRecord();
        $this->schema(CustomFormRenderForm::generateFormSchema($record->customForm,$this->getViewMode(),$this->getVariation()));
        $this->mutateRelationshipDataBeforeFillUsing(fn($data)=> CustomFormRenderForm::loadHelper($record));
        $this->mutateRelationshipDataBeforeSaveUsing(fn($data)=> CustomFormRenderForm::saveHelper($record, $data,$this->getVariation()));
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
