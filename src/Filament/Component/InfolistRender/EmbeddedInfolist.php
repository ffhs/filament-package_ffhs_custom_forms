<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\InfolistRender;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UsePosSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\UseViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\FormCompiler\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;

class EmbeddedInfolist extends Component
{

    protected string $view = 'filament-infolists::components.group';
    protected CustomFormAnswer|Closure $model;

    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;
    use UseViewMode;

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

        $this->setupSchema();


        $this->columns(1);


    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (EmbeddedInfolist $component){
            $customForm = CustomForm::cached($component->getModel()->custom_form_id);
            return $customForm->getFormConfiguration()::displayViewMode();
        };
        return $this;
    }

    public function getModel(): CustomFormAnswer {
        return $this->evaluate($this->model);
    }

    private function setupSchema(): void {
        if($this->isUseLayoutTypeSplit())$this->setSplitLayoutInfolistSchema();
        else if($this->isUseFieldSplit())$this->setSplitFieldInfolistSchema();
        else if($this->isUsePoseSplit())$this->setSplitPosInfolistSchema();
        else$this->setDefaultInfolistSchema();
    }

    /**
     * @return void
     */
    private function setSplitPosInfolistSchema(): void {
        $this->schema(fn(EmbeddedInfolist $component) => [
            Group::make()->schema(function (CustomFormAnswer|null $record) use ($component) {
                if (is_null($record)) return [];

                [$beginPos, $endPos] = $this->getPoseSpilt();

                return SplitCustomFormRender::renderInfolistPose(
                    $beginPos,
                    $endPos,
                    $component->getModel(),
                    $component->getViewMode()
                );
            }),
        ]);
    }

    /**
     * @return void
     */
    private function setDefaultInfolistSchema(): void {
        $this->schema(fn(EmbeddedInfolist $component) => [
            Group::make(fn($record) => CustomFormRender::generateInfoListSchema($component->getModel(),
                $component->getViewMode())),
        ]);
    }

    /**
     * @return void
     */
    private function setSplitFieldInfolistSchema(): void {
        $this->schema(fn(EmbeddedInfolist $component) => [
            Group::make(fn($record) => SplitCustomFormRender::renderInfolistFromField(
                $component->getFieldSplit(),
                $component->getModel(),
                $component->getViewMode())),
        ]);
    }

    /**
     * @return void
     */
    private function setSplitLayoutInfolistSchema(): void {
        $this->schema(fn(EmbeddedInfolist $component) => [
            Group::make(fn($record) => SplitCustomFormRender::renderInfoListLayoutType(
                $component->getLayoutTypeSplit(),
                $component->getModel(),
                $component->getViewMode())),
        ]);
    }


}
