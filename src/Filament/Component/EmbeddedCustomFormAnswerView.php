<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;

class EmbeddedCustomFormAnswerView extends Component
{

    protected string $view = 'filament-infolists::components.group';

    protected string|Closure $viewMode;
    protected CustomFormAnswer|Closure $model;

    public static function make(CustomFormAnswer|Closure $model, string|Closure $viewMode = "default"): static
    {
        $static = app(static::class, [
            'model' => $model,
            'viewMode'=>$viewMode
        ]);
        $static->configure();

        return $static;
    }

    final public function __construct(CustomFormAnswer|Closure $model,string|Closure $viewMode = "default")
    {
        $this->model= $model;
        $this->viewMode=$viewMode;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->label("");
        $this->schema(fn(EmbeddedCustomFormAnswerView $component)=>[
            Group::make(fn($record)=> CustomFormRender::generateInfoListSchema($component->getModel(),$component->getViewMode())),
        ]);
        $this->columns(1);


    }

    public function getViewMode(): string|Closure {
        return $this->evaluate($this->viewMode);
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (EmbeddedCustomFormAnswerView $component){
            $customForm = CustomForm::cached($component->getModel()->custom_form_id);
            return $customForm->getFormConfiguration()::displayViewMode();
        };
        return $this;
    }

    public function setViewMode(string|Closure $viewMode): void {
        $this->viewMode = $viewMode;
    }

    public function getModel(): CustomFormAnswer {
        return $this->evaluate($this->model);
    }





}
