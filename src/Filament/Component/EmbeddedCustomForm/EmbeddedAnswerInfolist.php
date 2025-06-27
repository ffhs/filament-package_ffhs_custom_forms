<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Filament\Component\EmbeddedCustomForm;


use Closure;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomForm;
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

    public static function make(string|Closure $viewMode = 'default'): static
    {
        $static = app(static::class, ['viewMode' => $viewMode]);
        $static->configure();
        $static->customFormAnswer(fn($record) => $record);

        return $static;
    }

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        return parent::getChildComponentContainer($key)
            ->record($this->getCustomFormAnswer());
    }

    public function customFormAnswer(CustomFormAnswer|Closure $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCustomFormAnswer(): CustomFormAnswer
    {
        return $this->evaluate($this->answer);
    }

    public function getCustomForm(): CustomForm
    {
        return $this->getCustomFormAnswer()->customForm;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->columns(1)
            ->schema(fn() => once(fn() => $this->getCustomFormSchema()))
            ->label('')
            ->autoViewMode();
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'viewMode' => [$this->getViewMode()],
            'customForm' => [$this->getCustomForm()],
            'customFormAnswer' => [$this->getCustomFormAnswer()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName)
        };
    }
}
