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

class EmbeddedAnswerInfolist extends Component
{
    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;
    use UseViewMode;


    protected string $view = 'filament-infolists::components.group';
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
        $this->columns(1);
        $this->label("");
        $this->setupSchema();
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (EmbeddedAnswerInfolist $component){
            $customForm = CustomForm::cached($component->getModel()->custom_form_id);
            return $customForm->getFormConfiguration()::displayViewMode();
        };
        return $this;
    }

    public function getModel(): CustomFormAnswer {
        return $this->evaluate($this->model);
    }

    private function setupSchema(): void {
        $this->columns(1);
        $this->schema(function(EmbeddedAnswerInfolist $component){
            if ($this->isUseLayoutTypeSplit()) return $this->getSplitLayoutInfolistSchema($component);
            //Field Splitting
            else if ($this->isUseFieldSplit()) return $this->getSplitFieldInfolistSchema($component);
            //Position Splitting
            else if ($this->isUsePoseSplit()) return $this->getSplitPosInfolistSchema($component);
            //Default
            else return $this->getDefaultInfolistSchema($component);
        });

    }


    private function getSplitPosInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
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
        ];
    }


    private function getDefaultInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make(fn($record) => CustomFormRender::generateInfoListSchema($component->getModel(),
                $component->getViewMode())),
        ];
    }


    private function getSplitFieldInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make(fn($record) => SplitCustomFormRender::renderInfolistFromField(
                $component->getFieldSplit(),
                $component->getModel(),
                $component->getViewMode())),
        ];
    }


    private function getSplitLayoutInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make(fn($record) => SplitCustomFormRender::renderInfoListLayoutType(
                $component->getLayoutTypeSplit(),
                $component->getModel(),
                $component->getViewMode())),
        ];
    }


}
