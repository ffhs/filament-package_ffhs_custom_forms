<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFormAnswer;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\HasViewMode;
use Ffhs\FilamentPackageFfhsCustomForms\Traits\UseSplitInfolistSchema;
use Filament\Infolists\ComponentContainer;
use Filament\Infolists\Components\Component;

class EmbeddedAnswerInfolist extends Component
{
    use UseSplitInfolistSchema;
    use HasViewMode;

    protected string $view = 'filament-infolists::components.group';
    protected CustomFormAnswer|Closure $answer;

    public static function make(string|Closure $viewMode = "default"): static
    {
        $static = app(static::class, [
            'viewMode' => $viewMode
        ]);
        $static->configure();
        $static->answer(fn($record) => $record);

        return $static;
    }


    public function getChildComponentContainer($key = null): ComponentContainer
    {
        return parent::getChildComponentContainer($key)
            ->record($this->getAnswer());
    }

    public function autoViewMode(bool|Closure $autoViewMode = true): static
    {
        if (!$this->evaluate($autoViewMode)) {
            return $this;
        }
        $this->viewMode = function (EmbeddedAnswerInfolist $component) {
            $customForm = $component->getAnswer()->customForm;
            return $customForm->getFormConfiguration()->displayViewMode();
        };
        return $this;
    }

    public function viewMode(string|Closure $viewMode = "default"): static
    {
        $this->viewMode = $viewMode;
        return $this;
    }

    public function getViewMode(): string
    {
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->columns(1);
        $this->schema(fn() => once(fn() => $this->getCustomFormSchema()));
        $this->label("");
    }
}
