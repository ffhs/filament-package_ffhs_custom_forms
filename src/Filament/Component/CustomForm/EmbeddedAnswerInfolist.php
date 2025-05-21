<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm;


use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\CustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\CustomForm\Render\SplitCustomFormRender;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\CustomFormLoadHelper;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\UseFieldSplit;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\UseLayoutSplit;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\UsePosSplit;
use Ffhs\FilamentPackageFfhsCustomForms\CustomForms\CustomForm\RenderHelp\UseViewMode;
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



    protected function getSplitPosInfolistSchema(): array {
        [$beginPos, $endPos] = $this->getPoseSpilt();

        return SplitCustomFormRender::renderInfolistPose(
            $beginPos,
            $endPos,
            $this->getAnswer(),
            $this->getViewMode()
        );
    }


    protected function getDefaultInfolistSchema(): array {
        return CustomFormRender::generateInfoListSchema($this->getAnswer(), $this->getViewMode());
    }


    protected function getSplitFieldInfolistSchema(): array {
        return SplitCustomFormRender::renderInfolistFromField(
            $this->getFieldSplit(),
            $this->getAnswer(),
            $this->getViewMode()
        );
    }


    protected function getSplitLayoutInfolistSchema(): array {
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
