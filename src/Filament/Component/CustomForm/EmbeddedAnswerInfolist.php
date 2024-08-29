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

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        if (filled($key)) {
            return $this->getChildComponentContainers()[$key];
        }

        return ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->record($this->getAnswer())
            ->components($this->getChildComponents());
    }

    protected function setUp(): void {
        parent::setUp();
        $this->columns(1);
        $this->label("");
        $this->setupSchema();
    }

    public function autoViewMode(bool|Closure $autoViewMode = true):static {
        if(!$this->evaluate($autoViewMode)) return $this;
        $this->viewMode = function (EmbeddedAnswerInfolist $component) {
            $customForm = $component->getAnswer()->customForm;
            return $customForm->getFormConfiguration()::displayViewMode();
        };
        return $this;
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
                    $component->getAnswer(),
                    $component->getViewMode()
                );
            }),
        ];
    }


    private function getDefaultInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make(fn($record) => CustomFormRender::generateInfoListSchema($component->getAnswer(),
                $component->getViewMode())),
        ];
    }


    private function getSplitFieldInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make(fn($record) => SplitCustomFormRender::renderInfolistFromField(
                $component->getFieldSplit(),
                $component->getAnswer(),
                $component->getViewMode())),
        ];
    }


    private function getSplitLayoutInfolistSchema(EmbeddedAnswerInfolist $component): array {
        return [
            Group::make( SplitCustomFormRender::renderInfoListLayoutType(
                $component->getLayoutTypeSplit(),
                $component->getAnswer(),
                $component->getViewMode())
            ),
        ];
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
