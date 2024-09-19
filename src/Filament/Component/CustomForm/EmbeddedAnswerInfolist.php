<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UsePosSplit;
use Ffhs\FilamentPackageFfhsCustomForms\Helping\CustomForm\RenderHelp\UseViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Filament\Infolists\ComponentContainer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Model;

class EmbeddedAnswerInfolist extends Component
{
    use UseLayoutSplit;
    use UseFieldSplit;
    use UsePosSplit;
    use UseViewMode;

    protected string $view = 'filament-infolists::components.group';
    protected CustomFormAnswer|Closure $answer;

    public static function make(string|Closure $viewMode = "default"): static
    {
        $static = app(static::class, [
            'viewMode'=>$viewMode
        ]);
        $static->configure();
        $static->answer(fn($record) => $record);

        return $static;
    }

    public function getChildComponents(): array
    {
        if(!is_array($this->childComponents) || empty($this->childComponents)){
            if ($this->isUseLayoutTypeSplit()) $schema = $this->getSplitLayoutInfolistSchema();
            //Field Splitting
            else if ($this->isUseFieldSplit()) $schema = $this->getSplitFieldInfolistSchema();
            //Position Splitting
            else if ($this->isUsePoseSplit()) $schema = $this->getSplitPosInfolistSchema();
            //Default
            else $schema = $this->getDefaultInfolistSchema();

            $this->childComponents = $schema;
        }

        return $this->childComponents;
    }


    public function getChildComponentContainer($key = null): ComponentContainer
    {
        return parent::getChildComponentContainer($key)
            ->record($this->getAnswer());
    }

    protected function setUp(): void {
        parent::setUp();
        $this->columns(1);
        $this->label("");
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (EmbeddedAnswerInfolist $component) {
            $customForm = $component->getAnswer()->customForm;
            return $customForm->getFormConfiguration()::displayViewMode();
        };
        return $this;
    }



    private function getSplitPosInfolistSchema(): array {
        [$beginPos, $endPos] = $this->getPoseSpilt();

        return SplitCustomFormRender::renderInfolistPose(
            $beginPos,
            $endPos,
            $this->getAnswer(),
            $this->getViewMode()
        );
    }


    private function getDefaultInfolistSchema(): array {
        return CustomFormRender::generateInfoListSchema($this->getAnswer(), $this->getViewMode());
    }


    private function getSplitFieldInfolistSchema(): array {
        return SplitCustomFormRender::renderInfolistFromField(
            $this->getFieldSplit(),
            $this->getAnswer(),
            $this->getViewMode()
        );
    }


    private function getSplitLayoutInfolistSchema(): array {
        return SplitCustomFormRender::renderInfoListLayoutType(
            $this->getLayoutTypeSplit(),
            $this->getAnswer(),
            $this->getViewMode()
        );
    }

    public function viewMode(string|Closure $viewMode = "default"): static {
        $this->viewMode = $viewMode;
        return $this;
    }

    public function getViewMode(): string {
        return $this->evaluate($this->viewMode);
    }

    public function answer(CustomFormAnswer|Closure $answer): static
    {
        $this->answer = $answer;
        return $this;
    }

    public function getAnswer(): CustomFormAnswer
    {
        return $this->evaluate($this->answer);
    }




}
